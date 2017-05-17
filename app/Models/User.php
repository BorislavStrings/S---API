<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password', 'profession', 'phone', 'image_id', 'cv_id'];
    protected $hidden = ['password'];

    public function techSkills() {
        return $this->hasMany('App\Models\UserTechSkills', 'user_id');
    }

    public function languages() {
        return $this->hasMany('App\Models\UserLanguages', 'user_id');
    }

    public function softSkills() {
        return $this->hasMany('App\Models\UserSoftSkills', 'user_id');
    }

    public function image() {
        return $this->belongsTo('App\Models\Files', 'image_id');
    }

    public function cv() {
        return $this->hasMany('App\Models\UserCV', 'user_id');
    }

    public function locations() {
        return $this->hasMany('App\Models\UserLocations', 'user_id');
    }

    public function devices() {
        return $this->hasMany('App\Models\UsersDevices', 'user_id');
    }

    public function appliedOffers() {
        return $this->hasMany('App\Models\UserApply', 'user_id');
    }
}
