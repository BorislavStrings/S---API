<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCV extends Model
{
    protected $table = 'users_cv';
    protected $fillable = ['user_id', 'file_id', 'version'];

    public function file() {
        return $this->belongsTo('App\Models\Files', 'file_id');
    }

    public function user() {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
