<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreMaterialIssued extends Model
{
    use HasFactory;

    protected $table = 'store_material_issued';

    protected $guarded = [];

    public function store()
    {
        return $this->belongsTo(Project::class, 'store_id');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'store_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
    
    public function history()
    {
        return $this->hasMany(MaterialIssuedHistory::class, 'issued_id');
    }
}
