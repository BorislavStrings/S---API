<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locations extends Model
{
    protected $table = 'locations';
    protected $fillable = ['name', 'lng', 'lat', 'place_id'];
}
