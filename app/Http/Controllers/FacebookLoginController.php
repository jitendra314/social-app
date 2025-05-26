<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Http;

class FacebookLoginController extends Controller
{
    public function redirectToFacebook()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleFacebookCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->stateless()->user();
            $accessToken = $facebookUser->token;
            $facebookId = $facebookUser->getId();

            // Get actual Facebook profile picture
            $response = \Http::get("https://graph.facebook.com/v19.0/{$facebookId}/picture", [
                'redirect' => false,
                'type' => 'large',
                'access_token' => $accessToken,
            ]);

            $avatarUrl = $response->json('data.url') ?? $facebookUser->getAvatar(); // fallback to default avatar

            // Find user by Facebook ID or email
            $user = User::where('facebook_id', $facebookId)->first();

            if (!$user) {
                $user = User::where('email', $facebookUser->getEmail())->first();

                if ($user) {
                    $user->update([
                        'facebook_id' => $facebookId,
                        'avatar' => $avatarUrl,
                    ]);
                } else {
                    $user = User::create([
                        'name' => $facebookUser->getName(),
                        'email' => $facebookUser->getEmail() ?? $facebookId . '@facebook.com',
                        'facebook_id' => $facebookId,
                        'avatar' => $avatarUrl,
                        'password' => bcrypt(uniqid()),
                    ]);
                }
            }else{
                $user = User::where('email', $facebookUser->getEmail())->first();

                if ($user) {
                    $user->update([
                        'facebook_id' => $facebookId,
                        'avatar' => $avatarUrl,
                    ]);
                }
            }

            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Logged in via Facebook!');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Facebook login failed!');
        }
    }

}
