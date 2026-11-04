<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentToken extends Model
{
    protected $table = 'assessment_tokens';
    protected $primaryKey = 'id';

    protected $guarded = [];

    protected $casts = [
        'expired_at' => 'datetime',
        'is_active' => 'boolean'
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class, 'assessment_id', 'assessment_id');
    }

    /**
     * Scope untuk mengecek token yang masih aktif dan belum expired
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where('expired_at', '>', now());
    }
}
