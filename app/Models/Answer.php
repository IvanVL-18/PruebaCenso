<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Answer extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'answers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'response',
        'answer', 
        'user_id', 
        'index_for_censo_has_question_id', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); 
    }

    public function indexForCensoHasQuestion()
    {
        return $this->belongsTo(IndexForCensosHasQuestion::class, 'index_for_censo_has_question_id'); 
    }
}


