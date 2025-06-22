<?php
namespace App\Http\Controllers;

use App\Models\Game;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Game $game)
    {
        // Check if user is part of the game
        if ($game->player1_id !== Auth::id() && $game->player2_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        Comment::create([
            'author_id' => Auth::id(),
            'game_id' => $game->id,
            'content' => $request->content,
        ]);

        return back()->with('success', 'Reactie toegevoegd!');
    }

    public function destroy(Comment $comment)
    {
        if ($comment->author_id !== Auth::id()) {
            abort(403);
        }

        $comment->delete();

        return back()->with('success', 'Reactie verwijderd!');
    }
}
