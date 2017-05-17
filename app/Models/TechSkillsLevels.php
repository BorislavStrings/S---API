<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TechSkillsLevels extends Model
{
    protected $table = 'tech_skills_levels';
    protected $fillable = ['name', 'value'];
}
