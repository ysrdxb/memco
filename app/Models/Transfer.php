<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\SoftDeletesTrait;

class Transfer extends Model
{
    use SoftDeletes, SoftDeletesTrait;

    protected $guarded = [];

    const STATUS_COMPLETED = 'completed';
    const STATUS_PENDING = 'pending';
    const STATUS_PROCESSING = 'processing';
    const STATUS_CANCELLED = 'cancelled';
        
    public function store()
    {
        return $this->belongsTo(Project::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function details()
    {
        return $this->hasMany(TransferDetail::class, 'transfer_id');
    }
    
    public function purchase()
    {
        return $this->hasOne(Purchase::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function delivery()
    {
        return $this->hasOne(DeliveryOrder::class, 'transfer_id');
    }  
    
    public function deliveryOrders()
    {
        return $this->hasMany(DeliveryOrder::class, 'transfer_id');
    }    

    public function warehouseStockRecords()
    {
        return $this->warehouse->stockRecords()
            ->where('product_id', $this->product_id);
    }    
    
    public function requestable()
    {
        return $this->morphTo();
    }    
    
    public function voucher()
    {
        return $this->hasOne(TransferVoucher::class, 'transfer_id');
    }    
      
}