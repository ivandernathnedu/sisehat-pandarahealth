<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentRecommendation extends Model
{
    protected $table = 'assessment_recommendations';
    protected $primaryKey = 'id';

    public $timestamps = false;
    protected $guarded = [];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id', 'assessment_id');
    }

    public function recommendation()
    {
        return $this->belongsTo(Recommendation::class, 'recommendation_id', 'recommendation_id');
    }
}
