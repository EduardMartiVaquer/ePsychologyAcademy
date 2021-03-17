<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassUser extends Model
{
    use HasFactory;

    protected $fillable = [
        "course",
        "subject",
        "user",
        "type", // 0 => admin, 1 => student, 2 => teacher, 3 => patient, 4 => psychologist
        "class",
    ];

    public function scopeWithAll($query){
        $query->with('course', 'subject', 'user', 'class');
    }

    public function course(){
        return $this->belongsTo(Course::class, 'course');
    }

    public function subject(){
        return $this->belongsTo(Subject::class, 'subject');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user');
    }

    public function class(){
        return $this->belongsTo(ClassEvent::class, 'class');
    }
}
