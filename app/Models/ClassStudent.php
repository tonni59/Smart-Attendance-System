<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassStudent extends Model
{
    use HasFactory;

    protected $table = 'class_students';

    protected $fillable = [
        'class_token',
        'student_token',
    ];

    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }
}
