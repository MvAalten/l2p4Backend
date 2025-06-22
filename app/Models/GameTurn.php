<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GameTurn extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player_id',
        'guessed_word',
        'turn_number',
        'result',
    ];

    protected $casts = [
        'result' => 'array',
    ];

    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    public function player()
    {
        return $this->belongsTo(User::class, 'player_id');
    }
}
