<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TechSkills;

class TechSkillsGroups extends Model
{
    protected $table = 'tech_skills_groups';
    protected $fillable = ['name'];

    public function skills()
    {
        return $this->hasMany('App\Models\TechSkills', 'group_id');
    }
}
