<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }    

    public function transfer()
    {
        return $this->belongsTo(Transfer::class, 'transfer_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function transferDetails()
    {
        return $this->belongsTo(TransferDetail::class, 'transfer_id', 'transfer_id');
    }

    public function deliveryOrder()
    {
        return $this->hasOne(DeliveryOrder::class, 'id');
    }

    public function requestable()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function serials()
    {
        return $this->hasMany(DeliveryOrderSerial::class);
    }    
}
