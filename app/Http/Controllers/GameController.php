<?php
namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\GameTurn;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GameController extends Controller
{
    private function getWordList($difficulty = 'medium')
    {
        $words = [
            'easy' => [
                'HUIS', 'BOOM', 'AUTO', 'BOEK', 'GELD', 'TIJD', 'WERK', 'SPEL',
                'ZOON', 'DEUR', 'HAND', 'HOOFD', 'VOET', 'GOED', 'GROOT', 'KLEIN'
            ],
            'medium' => [
                'PIANO', 'WATER', 'VUUR', 'GROEN', 'BLAUW', 'ROOD', 'ZWART', 'WIT',
                'SNEL', 'MOOI', 'LELIJK', 'MUZIEK', 'SCHOOL', 'KAMER', 'TAFEL', 'STOEL'
            ],
            'hard' => [
                'COMPLEXE', 'MYSTERIE', 'SYMFONIE', 'PHILOSOPHIE', 'TECHNOLOGIE',
                'ARCHITECTUUR', 'PSYCHOLOGIE', 'BIOLOGIE', 'ECONOMIE', 'LITERATUUR'
            ],
        ];

        return $words[$difficulty] ?? $words['medium'];
    }

    public function index()
    {
        $userId = Auth::id();

        $activeGames = Game::where(function ($query) use ($userId) {
            $query->where('player1_id', $userId)
                ->orWhere('player2_id', $userId);
        })->whereIn('status', ['waiting', 'active'])
            ->with(['player1', 'player2', 'turns'])
            ->get();

        // Get pending games separately (games waiting for acceptance)
        $pendingGames = Game::where(function ($query) use ($userId) {
            $query->where('player1_id', $userId)
                ->orWhere('player2_id', $userId);
        })->where('status', 'waiting')
            ->with(['player1', 'player2'])
            ->get();

        // Filter active games to only show truly active ones
        $activeGames = $activeGames->where('status', 'active');

        $finishedGames = Game::where(function ($query) use ($userId) {
            $query->where('player1_id', $userId)
                ->orWhere('player2_id', $userId);
        })->where('status', 'finished')
            ->with(['player1', 'player2', 'winner'])
            ->orderBy('ended_at', 'desc')
            ->take(10)
            ->get();

        return view('games.index', compact('activeGames', 'finishedGames', 'pendingGames'));
    }

    public function create()
    {
        // Fix: friends() returns a Collection already due to union() in the User model.
        // So just use the property 'friends' directly instead of calling get()
        $friends = Auth::user()->friends ?? collect();

        return view('games.create', compact('friends'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opponent_type' => 'required|in:random,friend',
            'friend_id' => 'required_if:opponent_type,friend|exists:users,id',
        ]);

        $opponentId = null;

        if ($request->opponent_type === 'random') {
            $opponentId = $this->findRandomOpponent();
            if (!$opponentId) {
                return back()->with('error', 'Geen tegenstander beschikbaar.');
            }
        } else {
            $opponentId = $request->friend_id;
        }

        $difficulty = Auth::user()->settings->preference ?? 'medium';
        $wordList = $this->getWordList($difficulty);

        $game = Game::create([
            'player1_id' => Auth::id(),
            'player2_id' => $opponentId,
            'target_word' => $wordList[array_rand($wordList)],
            'status' => 'waiting',
        ]);

        return redirect()->route('games.show', $game);
    }

    public function show(Game $game)
    {
        $userId = Auth::id();
        if ($game->player1_id !== $userId && $game->player2_id !== $userId) {
            abort(403);
        }

        $game->load([
            'player1',
            'player2',
            'turns' => fn($query) => $query->orderBy('turn_number'),
            'comments.author'
        ]);

        return view('games.show', compact('game'));
    }

    public function acceptInvitation(Game $game)
    {
        if ($game->player2_id !== Auth::id() || $game->status !== 'waiting') {
            abort(403);
        }

        $game->update([
            'status' => 'active',
            'started_at' => now(),
        ]);

        return redirect()->route('games.show', $game);
    }

    public function makeGuess(Request $request, Game $game)
    {
        $request->validate([
            'guess' => 'required|string|size:5|alpha',
        ]);

        if ($game->status !== 'active') {
            return back()->with('error', 'Dit spel is niet actief.');
        }

        $userId = Auth::id();
        if ($game->player1_id !== $userId && $game->player2_id !== $userId) {
            abort(403);
        }

        // Load turns relationship to ensure the method works
        $game->load('turns');

        // Check if it's the user's turn
        if (!$game->isUserTurn($userId)) {
            return back()->with('error', 'Het is niet jouw beurt.');
        }

        $guess = strtoupper($request->guess);
        $targetWord = $game->target_word;

        // Get the total number of turns for this player
        $playerTurns = $game->turns()->where('player_id', $userId)->count();
        $turnNumber = $playerTurns + 1;

        if ($turnNumber > 6) {
            return back()->with('error', 'Je hebt al je beurten gebruikt.');
        }

        $result = $this->checkGuess($guess, $targetWord);

        GameTurn::create([
            'game_id' => $game->id,
            'player_id' => $userId,
            'guessed_word' => $guess,
            'turn_number' => $game->turns()->count() + 1, // Global turn number
            'result' => $result,
        ]);

        if ($guess === $targetWord) {
            $game->update([
                'status' => 'finished',
                'winner_id' => $userId,
                'ended_at' => now(),
            ]);
            return back()->with('success', 'Gefeliciteerd! Je hebt het woord geraden!');
        } elseif ($turnNumber >= 6) {
            $opponent = $game->getOpponent($userId);
            $opponentTurns = $game->turns()->where('player_id', $opponent->id)->count();

            if ($opponentTurns >= 6) {
                $game->update([
                    'status' => 'finished',
                    'ended_at' => now(),
                ]);
                return back()->with('info', 'Spel beëindigd - beide spelers hebben al hun beurten gebruikt.');
            }
        }

        return back()->with('success', 'Gok ingestuurd!');
    }

    private function findRandomOpponent()
    {
        return User::where('id', '!=', Auth::id())
            ->withCount(['gamesAsPlayer1 as open_games' => function ($query) {
                $query->whereIn('status', ['waiting', 'active']);
            }])
            ->orderBy('open_games')
            ->first()?->id;
    }

    private function checkGuess($guess, $target)
    {
        $result = [];
        $targetArray = str_split($target);
        $guessArray = str_split($guess);
        $targetLetterCount = array_count_values($targetArray);

        // First pass: mark exact matches
        for ($i = 0; $i < 5; $i++) {
            if ($guessArray[$i] === $targetArray[$i]) {
                $result[$i] = 'correct';
                $targetLetterCount[$guessArray[$i]]--;
            }
        }

        // Second pass: mark present letters
        for ($i = 0; $i < 5; $i++) {
            if (!isset($result[$i])) { // Not already marked as correct
                if (isset($targetLetterCount[$guessArray[$i]]) && $targetLetterCount[$guessArray[$i]] > 0) {
                    $result[$i] = 'present';
                    $targetLetterCount[$guessArray[$i]]--;
                } else {
                    $result[$i] = 'absent';
                }
            }
        }

        return $result;
    }
}
