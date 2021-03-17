<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type', // 0 => admin, 1 => student, 2 => teacher, 3 => patient, 4 => psychologist
        'email',
        'code',
        'course',
        'subjects',
        'created_by'
    ];
}
