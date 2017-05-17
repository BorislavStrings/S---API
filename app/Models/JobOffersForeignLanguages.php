<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobOffersForeignLanguages extends Model
{
    protected $table = 'job_offers_foreign_languages';
    protected $fillable = ['offer_id', 'language_id', 'level_id', 'comment'];

    public function language()
    {
        return $this->belongsTo('App\Languages', 'language_id');
    }
}
