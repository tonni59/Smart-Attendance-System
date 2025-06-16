<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassAttendance extends Model
{

    protected $table = 'class_attendance';
    public $timestamps = false;
    protected $fillable = [
        'student_token',
        'class_token',
        'attendance_day',
        'status',
    ];
    
    use HasFactory;
}
