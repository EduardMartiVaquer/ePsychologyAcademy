<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'course',
        'name',
        'description',
        'created_by',
    ];

    public function scopeWithAll($query){
        $query->with('course', 'students', 'teachers', 'classes', 'files');
    }

    public function course(){
        return $this->belongsTo(Course::class, 'course');
    }

    public function subjectStudents(){
        return $this->hasMany(SubjectStudent::class, 'subject')->withAll();
    }

    public function students(){
        return $this->hasManyThrough(
            User::class,
            SubjectStudent::class,
            'user',
            'id',
            'id',
            'subject'
        );
    }

    public function subjectTeachers(){
        return $this->hasMany(SubjectTeacher::class, 'course')->withAll();
    }

    public function teachers(){
        return $this->hasManyThrough(
            User::class,
            SubjectTeacher::class,
            'user',
            'id',
            'id',
            'subject'
        );
    }

    public function classes(){
        return $this->hasMany(ClassEvent::class, 'subject')->withAll();
    }

    public function files(){
        return $this->hasMany(SubjectFile::class, 'subject');
    }
}
