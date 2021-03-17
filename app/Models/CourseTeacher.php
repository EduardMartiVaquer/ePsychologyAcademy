<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'course'
    ];

    public function user(){
        return $this->belongsTo(User::class, 'user');
    }

    public function course(){
        return $this->belongsTo(Course::class, 'course');
    }
}
