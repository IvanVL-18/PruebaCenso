<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Catalog extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'catalogs';  
    protected $primaryKey = 'id';  

    protected $fillable = [
        'name',
        'slug',
        'unit_id', 
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id'); 
    }
}


