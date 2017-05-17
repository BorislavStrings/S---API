<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLanguages extends Model
{
    protected $table = 'users_languages';
    protected $fillable = ['user_id', 'skill_id', 'level_id'];

    public function skill()
    {
        return $this->belongsTo('App\SoftSkills', 'skill_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
