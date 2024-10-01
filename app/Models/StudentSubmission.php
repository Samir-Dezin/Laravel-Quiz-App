<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        
        'name', 
        'email', 
        'phone', 
        'cv_path', 
        'status', 
        'accepted_at',
    ];
  

    // A student submission belongs to a user (student)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
