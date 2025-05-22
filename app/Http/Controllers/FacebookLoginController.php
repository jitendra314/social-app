<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

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

            // Check if user with this Facebook ID already exists
            $user = User::where('facebook_id', $facebookUser->getId())->first();

            // If not, check by email or create a new user
            if (!$user) {
                $user = User::where('email', $facebookUser->getEmail())->first();

                if ($user) {
                    // If user exists by email, update the Facebook ID
                    $user->update([
                        'facebook_id' => $facebookUser->getId()
                    ]);
                } else {
                    // Otherwise, create new user
                    $user = User::create([
                        'name' => $facebookUser->getName(),
                        'email' => $facebookUser->getEmail() ?? $facebookUser->getId().'@facebook.com', // Fallback email
                        'facebook_id' => $facebookUser->getId(),
                        'password' => bcrypt(uniqid()),
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
