<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Friendship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FriendController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $friends = $user->friends; // uses accessor
        $pendingRequests = $user->receivedFriendRequests()
            ->where('status', 'pending')
            ->with('requester')
            ->get();

        $sentRequests = $user->sentFriendRequests()
            ->where('status', 'pending')
            ->with('receiver')
            ->get();

        return view('friends.index', compact('friends', 'pendingRequests', 'sentRequests'));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');

        if (empty($query)) {
            return redirect()->route('friends.index');
        }

        $searchResults = User::where('id', '!=', Auth::id())
            ->where(function ($q) use ($query) {
                $q->where('username', 'LIKE', "%{$query}%")
                    ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->take(10)
            ->get();

        // Reload friends and requests to show on page
        $user = Auth::user();
        $friends = $user->friends;
        $pendingRequests = $user->receivedFriendRequests()
            ->where('status', 'pending')
            ->with('requester')
            ->get();
        $sentRequests = $user->sentFriendRequests()
            ->where('status', 'pending')
            ->with('receiver')
            ->get();

        return view('friends.index', compact('friends', 'pendingRequests', 'sentRequests', 'searchResults'));
    }

    public function sendRequest(Request $request)
    {
        $request->validate([
            'friend_id' => 'required|exists:users,id',
        ]);

        $friendId = $request->friend_id;

        if ($friendId == Auth::id()) {
            return back()->with('error', 'Je kunt jezelf niet als vriend toevoegen.');
        }

        // Check if friendship or request already exists
        $existingFriendship = Friendship::where(function ($query) use ($friendId) {
            $query->where('requester_id', Auth::id())
                ->where('receiver_id', $friendId);
        })->orWhere(function ($query) use ($friendId) {
            $query->where('requester_id', $friendId)
                ->where('receiver_id', Auth::id());
        })->first();

        if ($existingFriendship) {
            return back()->with('error', 'Er bestaat al een vriendschapsverzoek tussen jullie.');
        }

        Friendship::create([
            'requester_id' => Auth::id(),
            'receiver_id' => $friendId,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Vriendschapsverzoek verstuurd!');
    }

    public function acceptRequest($id)
    {
        $friendship = Friendship::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->update(['status' => 'accepted']);

        return back()->with('success', 'Vriendschapsverzoek geaccepteerd!');
    }

    public function declineRequest($id)
    {
        $friendship = Friendship::where('id', $id)
            ->where('receiver_id', Auth::id())
            ->where('status', 'pending')
            ->firstOrFail();

        $friendship->update(['status' => 'declined']);

        return back()->with('success', 'Vriendschapsverzoek afgewezen.');
    }
}
