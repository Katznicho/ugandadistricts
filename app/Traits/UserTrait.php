<?php

namespace App\Traits;

trait UserTrait
{

    public function getCurrentLoggedUserBySanctum()
    {
        $user =  auth('sanctum')->user()
            ? auth('sanctum')->user()
            : auth()->user();
        return $user;
    }
}
