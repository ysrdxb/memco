<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreItemLimit extends Model
{
    use HasFactory;

    protected $table = 'store_item_limits';

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function project()
    {
        return $this->belongsTo(project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
