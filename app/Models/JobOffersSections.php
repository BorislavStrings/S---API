<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOffersSections extends Model
{
    protected $table = 'job_offers_sections';
    protected $fillable = ['offer_id', 'name', 'data'];

    public function offer() {
        return $this->belongsTo('App\Models\JobOffers', 'offer_id');
    }
}
