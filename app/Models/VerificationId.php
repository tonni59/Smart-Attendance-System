<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationId extends Model
{
    use HasFactory;

    protected $table = 'verification_ids';
    
    protected $fillable = [
        'user_token',
        'photo',
    ];
}
