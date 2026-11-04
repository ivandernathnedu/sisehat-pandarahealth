<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $table = 'questions';
    protected $primaryKey = 'question_id';

    protected $guarded = [];

    

    public function factor()
    {
        return $this->belongsTo(Factor::class, 'factor_id', 'factor_id');
    }
}
