<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show(User $user)
    {
        // Check if profile is public or if it's the user's own profile or if they're friends
        $canView = $user->settings->is_profile_public ?? true;

        if (!$canView && Auth::id() !== $user->id) {
            // Check if they're friends
            $areFriends = Auth::user()->friends()->where('users.id', $user->id)->exists();
            if (!$areFriends) {
                abort(403, 'Dit profiel is privé.');
            }
        }

        $stats = [
            'total_games' => $user->games()->count(),
            'total_wins' => $user->getTotalWins(),
            'wins_today' => $user->getWinsToday(),
            'wins_this_week' => $user->getWinsThisWeek(),
            'win_rate' => $user->games()->count() > 0 ?
                round(($user->getTotalWins() / $user->games()->count()) * 100, 1) : 0,
        ];

        $recentGames = $user->games()
            ->where('status', 'finished')
            ->with(['player1', 'player2', 'winner'])
            ->orderBy('ended_at', 'desc')
            ->take(5)
            ->get();

        return view('profile.show', compact('user', 'stats', 'recentGames'));
    }
}
