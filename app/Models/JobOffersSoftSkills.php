<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOffersSoftSkills extends Model
{
    protected $table = 'job_offers_soft_skills';
    protected $fillable = ['offer_id', 'skill_id', 'level_id', 'comment'];

    public function skill()
    {
        return $this->belongsTo('App\Models\SoftSkills', 'skill_id');
    }

    public function level()
    {
        return $this->belongsTo('App\Models\SoftSkillsLevels', 'level_id');
    }

    public function offer() {
        return $this->belongsTo('App\Models\JobOffers', 'offer_id');
    }
}
