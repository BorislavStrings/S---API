<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticEvents extends Model
{
    protected $table = 'statistic_events';
    protected $fillable = ['event_inx', 'category_id'];

    public function category()
    {
        return $this->belongsTo('App\Models\StatisticEventsCategories', 'category_id');
    }
}
