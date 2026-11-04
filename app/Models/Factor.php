<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Factor extends Model
{
    protected $table = 'factors';
    protected $primaryKey = 'factor_id';

    protected $guarded = [];

    public function questions()
    {
        return $this->hasMany(Question::class, 'factor_id', 'factor_id');
    }
}
