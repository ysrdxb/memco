<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $guarded = [];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }  

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    
    public function details()
    {
        return $this->hasMany(PurchaseDetail::class);
    } 

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }    

    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'purchase_id');
    }
}

