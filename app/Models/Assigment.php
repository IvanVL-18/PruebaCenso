<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assigment extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'assigments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'institution_id', 
        'index_for_censo_id', 
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id'); 
    }

    public function indexForCenso()
    {
        return $this->belongsTo(IndexForCenso::class, 'index_for_censo_id'); 
    }
}


