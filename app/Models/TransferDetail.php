<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\SoftDeletesTrait;

class TransferDetail extends Model
{
    use HasFactory, SoftDeletes, SoftDeletesTrait;

    protected $guarded = [];

    public function transfer()
    {
        return $this->hasOne(Transfer::class, 'transfer_id');        
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }  
    
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }     
}
