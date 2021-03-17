<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'status',
        'type', // 0 => admin, 1 => student, 2 => teacher, 3 => patient, 4 => psychologist
        'name',
        'last_name',
        'email', 
        'phone_prefix',
        'phone',
        'password',
        'avatar',
        'timezone',
        'lang',
        'ip',
        'last_login',
        'push_notifications',
        'email_notifications',
        'sms_notifications',
        'languages',

        'courses',
        'subjects'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeWithAll($query){
        $query->with('classes');
    }

    public function studentCourses(){
        return $this->belongsToMany(Course::class, 'course_students', 'user', 'course')->as('course_student')->withPivot('course', 'user')->withAll();
    }

    public function teacherCourses(){
        return $this->belongsToMany(Course::class, 'course_teachers', 'user', 'course')->as('course_teacher')->withPivot('course', 'user')->withAll();
    }

    public function subjectStudent(){
        return $this->hasMany(SubjectStudent::class, 'user');
    }

    public function studentSubjects(){
        return $this->belongsToMany(Subject::class, 'subject_students', 'user', 'subject')->as('subject_student')->withPivot('user', 'subject')->withAll();
    }

    public function teacherSubjects(){
        return $this->belongsToMany(Subject::class, 'subject_teachers', 'user', 'subject')->as('subject_teacher')->withPivot('user', 'subject')->withAll();
    }

    public function classes(){
        return $this->belongsToMany(ClassEvent::class, 'class_users', 'user', 'class')->as('class_user')->withPivot('course', 'subject', 'user', 'type', 'class')->withAll();
    }

    public function canJoinClassCall($class_id){
        return !is_null(ClassUser::where('class', $class_id)->where('user', $this->id)->first());
    }
}
