<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Section extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sections';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'instructions',
        'is_extra',
    ];

    /**
     * Relación polimórfica
     */
    public function indexForCensos()
    {
        return $this->morphMany(IndexForCenso::class, 'reference');
    }
}

