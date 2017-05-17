<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserApply extends Model
{
    protected $table = 'users_apply';
    protected $fillable = ['user_id', 'offer_id', 'status'];

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function offer() {
        return $this->belongsTo('App\Models\JobOffers', 'offer_id');
    }
}