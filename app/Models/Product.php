<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function stockRecords()
    {
        return $this->hasMany(StockRecord::class, 'product_id', 'id');
    }
    
    public function purchase()
    {
        return $this->hasMany(Purchase::class);
    }
    
    public function purchaseDetails()
    {
        return $this->hasMany(PurchaseDetail::class, 'product_id');
    }    
    
    public function transferVouchers()
    {
        return $this->hasMany(DeliveryOrder::class, 'product_id');
    }      

    public function stock()
    {
        return $this->belongsTo(StockRecord::class);
    }

    public function materialRequestDetail()
    {
        return $this->hasMany(TransferDetail::class, 'product_id');
    }

    public function materialRequestDetails()
    {
        return $this->hasMany(TransferDetail::class, 'product_id');
    }    

}
