<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function details()
    {
        return $this->hasMany(MaterialRequestDetail::class, 'material_request_id');
    }

    public function deliveries()
    {
        return $this->hasMany(MaterialDelivery::class, 'material_request_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }    

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
    
    public function materialRequestDetails()
    {
        return $this->hasMany(MaterialRequestDetail::class, 'material_request_id');
    }   
}
