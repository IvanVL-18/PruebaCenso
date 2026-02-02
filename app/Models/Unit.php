<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'units';  
    protected $primaryKey = 'id';  

    protected $fillable = [
        'name',
    ];

    
     /**
     * Relación polimórfica
     */
    public function indexForCensos()
    {
        return $this->morphMany(IndexForCenso::class, 'reference');
    }

}


