<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'notify_by_email',
        'is_profile_public',
        'language',
        'preference',
    ];

    protected $casts = [
        'notify_by_email' => 'boolean',
        'is_profile_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
