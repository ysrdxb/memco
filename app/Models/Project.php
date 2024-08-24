<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $guarded = [];

    public function stockRecords()
    {
        return $this->morphMany(StockRecord::class, 'stockable');
    }

    public function materialRequests()
    {
        return $this->morphMany(Transfer::class, 'requestable');
    }  
    
    public function transfers()
    {
        return $this->morphMany(Transfer::class, 'requestable');
    }     

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_users');
    }
    
    public function subStores()
    {
        return $this->hasMany(SubStore::class);
    }     
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
