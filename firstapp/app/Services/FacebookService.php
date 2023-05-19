<?php

namespace App\Services;

use App\Models\FacebookUser;

class FacebookService
{
    public function __construct(
        protected FacebookUser $facebookUser
    ) {
    }

     /**
     * store facebook
     *
     * @param array $data
     * @return FacebookUser
     */
    public function store(array $data)
    {
        $fbData = [
            "fbId" => $data["fb"]["id"],
            "name" => $data["fb"]["name"],
            "friends" => json_encode($data["friends"]["data"])
        ];
        dd($fbData);
        $fbData = $this->facebookUser->create($fbData);
        return $fbData;
    }
}

?>