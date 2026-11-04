<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FactorScore extends Model
{
    protected $table = 'factor_scores';
    protected $primaryKey = 'factor_score_id';

    public $timestamps = false;

    protected $guarded = [];

    public function healthScore()
    {
        return $this->belongsTo(HealthScore::class, 'health_score_id', 'health_score_id');
    }

    public function factor()
    {
        return $this->belongsTo(Factor::class, 'factor_id', 'factor_id');
    }
}
