<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseDetail extends Model
{
    use HasFactory;
    
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }    

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }   

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }      
}
