<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockDetail extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function stockRecord()
    {
        return $this->belongsTo(StockRecord::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function deliveryOrderSerials()
    {
        return $this->hasMany(DeliveryOrderSerial::class, 'stock_detail_id');
    }
}