<?php

namespace App\Helpers;

use Laravel\Socialite\Two\FacebookProvider;

class SocialConnect extends FacebookProvider {

    public function userFromToken($access_token)
    {
        $user = $this->mapUserToObject($this->getUserByToken($access_token));

        return $user->setToken($access_token);
    }
}