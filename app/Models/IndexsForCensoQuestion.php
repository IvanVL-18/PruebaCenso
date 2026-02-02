<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndexForCensoQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'index_for_censo_question';
    protected $primaryKey = 'id';

    protected $fillable = [
        'question_id', 
        'index_for_censo_id', 
    ];

    public function question()
    {
        return $this->belongsTo(Question::class, 'question_id'); 
    }

    public function indexForCenso()
    {
        return $this->belongsTo(IndexForCenso::class, 'index_for_censo_id'); 
    }
}

