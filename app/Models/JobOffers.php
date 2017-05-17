<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOffers extends Model
{
    protected $table = 'job_offers';

    public function sections()
    {
        return $this->hasMany('App\Models\JobOffersSections', 'offer_id');
    }

    public function image()
    {
        return $this->hasOne('App\Models\Files', 'id', 'image');
    }

    public function languages()
    {
        return $this->hasMany('App\Models\JobOffersForeignLanguages', 'offer_id');
    }

    public function locations()
    {
        return $this->hasMany('App\Models\JobOffersLocations', 'offer_id');
    }

    public function softSkills()
    {
        return $this->hasMany('App\Models\JobOffersSoftSkills', 'offer_id');
    }

    public function techSkills()
    {
        return $this->hasMany('App\Models\JobOffersTechSkills', 'offer_id');
    }
}
