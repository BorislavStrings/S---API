<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticEventsCaptured extends Model
{
    protected $table = 'statistic_events_captured';
    protected $fillable = ['device', 'event_id'];

    public function device()
    {
        return $this->belongsTo('App\Models\UserDevices', 'device');
    }

    public function event()
    {
        return $this->belongsTo('App\Models\StatisticEvents', 'event_id');
    }
}
