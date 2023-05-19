<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;
use App\Models\FacebookUser;
use App\Models\FacebookUserPost;

class FacebookController extends Controller
{
    public function __construct(
        protected FacebookUser $facebookUser,
        protected FacebookUserPost $facebookUserPost
    ) {
    }

    public function login(Request $request)
    {
        $fbData = [
            "fbId" => $request->fb["id"],
            "name" => $request->fb["name"],
            "friends" => json_encode($request->friends["data"])
        ];

        $fbData = $this->facebookUser->query()
            ->updateOrCreate(["fbId" => $request->fb["id"]], $fbData);
        die(json_encode(["status" => "connected", "fbId" => $request->fb["id"]]));
    }

    public function posts(Request $request)
    {
        $fbId = $request->fbId;
        $fb = $this->facebookUser->where("fbId", $fbId)->first();

        $friends = collect(json_decode($fb->friends))->pluck("id")->toArray();
        $friends[] = $fbId;

        $friendsIds = $this->facebookUser->whereIn("fbId", $friends)
            ->get()
            ->pluck("id")
            ->toArray();

        $posts = $this->facebookUserPost->whereIn("fb_user_id", $friendsIds)->get();
      
        echo view('shopify.ajax.posts', ["posts" => $posts]);
        exit;
    }
}
