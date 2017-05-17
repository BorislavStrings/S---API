<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechSkills extends Model
{
    protected $table = 'tech_skills';
    protected $fillable = ['group_id', 'parent_id', 'name'];

    public function group()
    {
        return $this->belongsTo('App\Models\TechSkillsGroups', 'group_id');
    }
}
