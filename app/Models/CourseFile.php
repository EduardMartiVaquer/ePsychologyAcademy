<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'course',
        'name',
        'path',
        'short_path',
        'type',
        'extension'
    ];

    public function scopeWithAll($query){
        $query->with('course');
    }

    public function course(){
        return $this->belongsTo(Course::class, 'course');
    }
}
