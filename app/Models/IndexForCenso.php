<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndexForCenso extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'index_for_censos'; 
    protected $primaryKey = 'id';

    protected $fillable = [
        'censo_id', 
        'index_id', 
        'reference_type',
        'reference_id',
        'change',
    ];

    public function censo()
    {
        return $this->belongsTo(Censo::class, 'censo_id'); 
    }

    public function index()
    {
        return $this->belongsTo(Index::class, 'index_id'); 
    }

    // Polimórfica 
    public function reference()
    {
        return $this->morphTo(__FUNCTION__, 'reference_type', 'reference_id');
    }
}


