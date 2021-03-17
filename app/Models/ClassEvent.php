<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        "course",
        "subject",
        "from",
        "to",
        "name",
        "description",
        "opentok_session",
        "opentok_token"
    ];

    public function scopeWithAll($query){
        $query->with('course', 'subject', 'users', 'files');
    }

    public function course(){
        return $this->belongsTo(Course::class, 'course');
    }

    public function subject(){
        return $this->hasOne(Subject::class, 'id', 'subject');
    }

    public function classUsers()
    {
        return $this->hasMany(ClassUser::class, 'class')->withAll();
    }

    public function users(){
        return $this->belongsToMany(User::class, 'class_users', 'class', 'user')->as('class_user')->withPivot('course', 'subject', 'user', 'type', 'class');
    }

    public function files(){
        return $this->hasMany(ClassFile::class, 'class');
    }
}
