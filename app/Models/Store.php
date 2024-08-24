<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'projects';

    public function stockRecords()
    {
        return $this->morphMany(StockRecord::class, 'stockable');
    }

    public function materialRequests()
    {
        return $this->morphMany(MaterialRequest::class, 'requestable');
    }   

    public function users()
    {
        return $this->belongsToMany(ProjectUser::class, 'project_users');
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
