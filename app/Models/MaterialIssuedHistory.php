<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialIssuedHistory extends Model
{
    use HasFactory;

    protected $guarded = [];
    protected $table = 'material_issued_history';
    
    public function issued()
    {
        return $this->belongsTo(StoreMaterialIssued::class);
    }
}
