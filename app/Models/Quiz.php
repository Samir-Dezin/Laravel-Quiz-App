<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'description', 'activation_time', 'expiration_time'];

    // Relationships
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'quiz_user', 'quiz_id', 'user_id')->withTimestamps();
    }
}
