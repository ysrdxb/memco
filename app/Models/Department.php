<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function positions()
    {
        return $this->belongsToMany(Position::class, 'department_position');
    }
        
    public function users()
    {
        return $this->hasMany(User::class);
    }    
}
