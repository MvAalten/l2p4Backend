<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Game;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $leaderboardToday = User::withCount(['wonGames' => function ($query) {
            $query->whereDate('ended_at', today());
        }])->orderBy('won_games_count', 'desc')->take(5)->get();

        $leaderboardWeek = User::withCount(['wonGames' => function ($query) {
            $query->whereBetween('ended_at', [now()->startOfWeek(), now()->endOfWeek()]);
        }])->orderBy('won_games_count', 'desc')->take(5)->get();

        $leaderboardAllTime = User::withCount('wonGames')
            ->orderBy('won_games_count', 'desc')->take(5)->get();

        return view('welcome', compact('leaderboardToday', 'leaderboardWeek', 'leaderboardAllTime'));
    }
}
