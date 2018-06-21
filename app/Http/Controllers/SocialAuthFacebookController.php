<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Socialite;
use App\Services\SocialFacebookAccountService;

class SocialAuthFacebookController extends Controller
{
  /**
   * Create a redirect method to facebook api.
   *
   * @return void
   */
    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    /**
     * Return a callback method from facebook api.
     *
     * @return callback URL from facebook
     */
    public function callback(SocialFacebookAccountService $service)
    {
      $user = $service->createOrGetUser(Socialite::driver('facebook')->user());
      $x = Socialite::driver('facebook')->user();
      var_dump($user->token);
      echo "<br><hr>";
      var_dump($user->refreshToken);
      echo "<br><hr>";
      var_dump($user->tokenSecret); exit;
      echo "<br><hr>";
      var_dump($x->token); exit;
        auth()->login($user);
        return redirect()->to('/home');

    }
}
