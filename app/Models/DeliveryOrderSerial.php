<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrderSerial extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function deliveryOrder()
    {
        return $this->belongsTo(DeliveryOrder::class, 'delivery_order_id');
    }    

    public function serial()
    {
        return $this->belongsTo(StockDetail::class, 'stock_detail_id');
    }
    
    public function stockDetail()
    {
        return $this->belongsTo(StockDetail::class, 'stock_detail_id');
    }    

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
        
}