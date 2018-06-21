<?php

namespace App\Services;
use App\SocialFacebookAccount;
use App\User;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialFacebookAccountService
{
    public function createOrGetUser(ProviderUser $providerUser)
    {//exit('test');
        $account = SocialFacebookAccount::whereProvider('facebook')
            ->whereProviderUserId($providerUser->getId())
            ->first();
        var_dump($providerUser->token);exit;
        if ($account) {
            return $account->user;
        } else {

            $account = new SocialFacebookAccount([
                'provider_user_id' => $providerUser->getId(),
                'provider' => 'facebook'
                //'nickname' => $providerUser->getNickname(),
                //'avatar' => $providerUser->avatar_original,
                //'token' => $providerUser->token,
            ]);

            $user = User::whereEmail($providerUser->getEmail())->first();

            if (!$user) {

                $user = User::create([
                    'email' => $providerUser->getEmail(),
                    'name' => $providerUser->getName(),
                    'password' => bcrypt('ilham'),
                ]);
            }

            $account->user()->associate($user);
            $account->save();
            $user->token = $providerUser->token;

            return $user;
        }
    }
}
