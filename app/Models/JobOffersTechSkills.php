<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOffersTechSkills extends Model
{
    protected $table = 'job_offers_tech_skills';
    protected $fillable = ['offer_id', 'skill_id', 'min_experience', 'max_experience', 'level_id', 'comment'];

    public function skill()
    {
        return $this->belongsTo('App\Models\TechSkills', 'skill_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\TechSkillsLevels', 'level_id');
    }

    public function offer() {
        return $this->belongsTo('App\Models\JobOffers', 'offer_id');
    }
}
