<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Umkm extends Model
{
    protected $table = 'umkms';
    protected $primaryKey = 'umkm_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $guarded = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $latest = self::orderBy('umkm_id', 'desc')->first();
                if (!$latest) {
                    $number = 1;
                } else {
                    // Ambil angka dari ID terakhir (misal UMKM005 -> 5)
                    $lastNumber = (int) substr($latest->umkm_id, 4);
                    $number = $lastNumber + 1;
                }
                $model->{$model->getKeyName()} = 'UMKM' . str_pad($number, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class, 'owner_id', 'owner_id');
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class, 'umkm_id', 'umkm_id');
    }
}
