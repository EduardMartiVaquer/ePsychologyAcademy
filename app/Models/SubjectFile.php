<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject',
        'name',
        'path',
        'short_path',
        'type',
        'extension'
    ];

    public function scopeWithAll($query){
        $query->with('subject');
    }

    public function subject(){
        return $this->belongsTo(Subject::class, 'subject');
    }
}
