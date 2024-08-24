<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_position');
    }
        
    public function users()
    {
        return $this->hasMany(User::class);
    }     
}
