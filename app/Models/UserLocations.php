<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserLocations extends Model
{
    protected $table = 'users_locations';
    protected $fillable = ['user_id', 'location_id', 'type'];

    public function user()
    {
        return $this->belongsTo('App\Models\user', 'user_id');
    }

    public function location()
    {
        return $this->belongsTo('App\Models\Locations', 'location_id');
    }

    public $timestamps = false;
}
