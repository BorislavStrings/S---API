<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOffersLocations extends Model
{
    protected $table = 'job_offers_locations';
    protected $fillable = ['offer_id', 'location_id'];

    public function offer()
    {
        return $this->belongsTo('App\Models\JobOffers', 'offer_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Locations', 'location_id');
    }
}
