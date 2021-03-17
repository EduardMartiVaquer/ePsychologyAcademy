<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'class',
        'name',
        'path',
        'short_path',
        'type',
        'extension'
    ];

    public function scopeWithAll($query){
        $query->with('class');
    }

    public function class(){
        return $this->belongsTo(ClassEvent::class, 'class');
    }
}
