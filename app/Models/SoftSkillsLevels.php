<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftSkillsLevels extends Model
{
    protected $table = 'soft_skills_levels';
    protected $fillable = ['name', 'value'];
}
