<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Censo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'censos';  
    protected $primaryKey = 'id';  

    protected $fillable = [
        'name',
        'description',
        'init_date',
        'deadline',
    ];

    protected $dates = [
        'init_date',
        'deadline',
        'deleted_at',
    ];
}

