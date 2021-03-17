<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectTeacher extends Model
{
    use HasFactory;

    protected $fillable = [
        'user',
        'subject'
    ];

    public function scopeWithAll($query){
        $query->with('user', 'subject');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user');
    }

    public function subject(){
        return $this->belongsTo(Subject::class, 'subject');
    }
}
