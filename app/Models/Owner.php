<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Owner extends Authenticatable
{
    protected $table = 'owners';
    protected $primaryKey = 'owner_id';

    protected $guarded = [];

    protected $hidden = ['password'];

    

    public function getGenderAttribute($value)
    {
        if ($value === 'male') return 'laki-laki';
        if ($value === 'female') return 'perempuan';
        return $value;
    }

    public function umkms()
    {
        return $this->hasMany(Umkm::class, 'owner_id', 'owner_id');
    }
}
