<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTechSkills extends Model
{
    protected $table = 'users_tech_skills';
    protected $fillable = ['user_id', 'skill_id', 'level_id', 'min_experience', 'max_experience'];

    public function skill()
    {
        return $this->belongsTo('App\TechSkills', 'skill_id');
    }

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }
}
