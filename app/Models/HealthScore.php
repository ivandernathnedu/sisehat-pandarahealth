<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthScore extends Model
{
    protected $table = 'health_scores';
    protected $primaryKey = 'health_score_id';

    public $timestamps = false;

    protected $guarded = [];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id', 'assessment_id');
    }
}
