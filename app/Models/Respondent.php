<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Respondent extends Model
{
    protected $table = 'respondents';
    protected $primaryKey = 'respondent_id';

    protected $guarded = [];

    public function responses()
    {
        return $this->hasMany(Response::class, 'respondent_id', 'respondent_id');
    }
}
