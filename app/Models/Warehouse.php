<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function stockRecords()
    {
        return $this->morphMany(StockRecord::class, 'stockable');
    }    
    
    public function stockable()
    {
        return $this->morphTo();
    }

    public function materialRequests()
    {
        return $this->morphMany(MaterialRequest::class, 'requestable');
    }    

    public function users()
    {
        return $this->belongsToMany(User::class, 'warehouse_users');
    }  

    public function purchases()
    {
        return $this->hasMany(Purchase::class, 'warehouse_id');
    }

    public function transfers()
    {
        return $this->morphMany(Transfer::class, 'requestable');
    }       
       
}
