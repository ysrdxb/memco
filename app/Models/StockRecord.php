<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRecord extends Model
{
    use HasFactory;

    protected $fillable = ['quantity', 'stockable_id', 'stockable_type', 'product_id', 'material_request_no', 'delivery_order_no', 'remarks','unit_id'];

    protected $table = 'stock_records';

    public function stockable()
    {
        return $this->morphTo();
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }

    public function scopeForStore($query, $storeId)
    {
        return $query->where('stockable_id', $storeId)
            ->where('stockable_type', Project::class);
    }  
    
    public function detail()
    {
        return $this->hasMany(StockDetail::class);
    }

    public function stockDetails()
    {
        return $this->hasMany(StockDetail::class);
    }   

}