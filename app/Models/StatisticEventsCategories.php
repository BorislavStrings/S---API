<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StatisticEventsCategories extends Model
{
    protected $table = 'statistic_events_categories';
    protected $fillable = ['name'];

    public function category()
    {
        return $this->hasMany('App\Models\StatisticEvents', 'category_id');
    }
}
