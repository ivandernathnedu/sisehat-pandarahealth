<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assessment extends Model
{
    protected $table = 'assessments';
    protected $primaryKey = 'assessment_id';

    protected $guarded = [];

    

    public function umkm()
    {
        return $this->belongsTo(Umkm::class, 'umkm_id', 'umkm_id');
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'assessment_id', 'assessment_id');
    }

    public function healthScore()
    {
        return $this->hasOne(HealthScore::class, 'assessment_id', 'assessment_id');
    }
}
