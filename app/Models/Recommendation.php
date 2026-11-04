<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    protected $table = 'recommendations';
    protected $primaryKey = 'recommendation_id';

    protected $guarded = [];

    public function factor()
    {
        return $this->belongsTo(Factor::class, 'factor_id', 'factor_id');
    }
}
