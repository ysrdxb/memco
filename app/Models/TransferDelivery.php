<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferDelivery extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $timestamps = false;

    public function transfer()
    {
        return $this->hasOne(Transfer::class);        
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }  
    
}
