<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        "description"
    ];

    public function scopeWithAll($query){
        $query->with('subjects', 'students', 'teachers', 'classes', 'files');
    }

    public function subjects(){
        return $this->hasMany(Subject::class, 'course')->withAll();
    }

    public function courseStudents(){
        return $this->hasMany(CourseStudent::class, 'course');
    }

    public function students(){
        return $this->hasManyThrough(
            User::class,
            CourseStudent::class,
            'course',
            'id',
            'id',
            'user'
        );
    }

    public function courseTeachers(){
        return $this->hasMany(CourseTeacher::class, 'course');
    }

    public function teachers(){
        return $this->hasManyThrough(
            User::class,
            CourseTeacher::class,
            'course',
            'id',
            'id',
            'user'
        );
    }

    public function classes(){
        return $this->hasMany(ClassEvent::class, 'course');
    }

    public function files(){
        return $this->hasMany(CourseFile::class, 'course');
    }
}
