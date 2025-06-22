<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'avatar',
        'registered_at',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'registered_at' => 'datetime',
        'last_login_at' => 'datetime',
    ];

    public function sentFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'requester_id');
    }

    public function receivedFriendRequests()
    {
        return $this->hasMany(Friendship::class, 'receiver_id');
    }

    // Friends where current user is the requester
    public function friendsOfMine()
    {
        return $this->belongsToMany(User::class, 'friendships', 'requester_id', 'receiver_id')
            ->wherePivot('status', 'accepted');
    }

    // Friends where current user is the receiver
    public function friendOf()
    {
        return $this->belongsToMany(User::class, 'friendships', 'receiver_id', 'requester_id')
            ->wherePivot('status', 'accepted');
    }

    // Method to get friends collection (for use with friends())
    public function friends()
    {
        return $this->friendsOfMine->merge($this->friendOf);
    }

    // Combined friends accessor (collection) - for use with $user->friends
    public function getFriendsAttribute()
    {
        return $this->friendsOfMine->merge($this->friendOf);
    }

    public function gamesAsPlayer1()
    {
        return $this->hasMany(Game::class, 'player1_id');
    }

    public function gamesAsPlayer2()
    {
        return $this->hasMany(Game::class, 'player2_id');
    }

    public function games()
    {
        return Game::query()
            ->where('player1_id', $this->id)
            ->orWhere('player2_id', $this->id);
    }

    public function wonGames()
    {
        return $this->hasMany(Game::class, 'winner_id');
    }

    public function settings()
    {
        return $this->hasOne(Setting::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'author_id');
    }

    public function getWinsToday()
    {
        return $this->wonGames()->whereDate('ended_at', today())->count();
    }

    public function getWinsThisWeek()
    {
        return $this->wonGames()->whereBetween('ended_at', [now()->startOfWeek(), now()->endOfWeek()])->count();
    }

    public function getTotalWins()
    {
        return $this->wonGames()->count();
    }
}
