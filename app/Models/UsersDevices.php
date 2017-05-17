<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersDevices extends Model
{
    protected $table = 'users_devices';
    protected $fillable = ['user_id', 'device', 'os', 'version'];

    public function user()
    {
        return $this->belongsTo('App\Models\user', 'user_id');
    }
}
