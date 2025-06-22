<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'player1_id',
        'player2_id',
        'status',
        'winner_id',
        'target_word',
        'started_at',
        'ended_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function player1()
    {
        return $this->belongsTo(User::class, 'player1_id');
    }

    public function player2()
    {
        return $this->belongsTo(User::class, 'player2_id');
    }

    public function winner()
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function turns()
    {
        return $this->hasMany(GameTurn::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getOpponent($userId)
    {
        return $this->player1_id == $userId ? $this->player2 : $this->player1;
    }

    public function isUserTurn($userId)
    {
        // If game is not active, no one's turn
        if ($this->status !== 'active') {
            return false;
        }

        // Get the most recent turn
        $lastTurn = $this->turns()->latest('id')->first();

        // If there's no turn yet, it's player1's turn
        if (!$lastTurn) {
            return $this->player1_id === $userId;
        }

        // If the last turn was by this user, it's now the opponent's turn
        if ($lastTurn->player_id === $userId) {
            return false;
        }

        // If the last turn was by the opponent, it's this user's turn
        return true;
    }

    public function getUserTurnCount($userId)
    {
        return $this->turns()->where('player_id', $userId)->count();
    }

    public function hasUserWon($userId)
    {
        return $this->winner_id === $userId;
    }

    public function isGameOver()
    {
        return $this->status === 'finished';
    }
}
