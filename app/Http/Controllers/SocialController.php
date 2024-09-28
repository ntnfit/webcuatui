<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
class SocialController extends Controller
{
    public function redirectToSocial($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
    public function handleSocialCallback($provider)
    {
        $socialUser = Socialite::driver($provider)->stateless()->user();
        $user = Customer::where('email', $socialUser->email)->first();
        if(!$user)
        {
            $user = Customer::create([
                'name' => $socialUser->name,
                'email' => $socialUser->email,
                'password' => \Hash::make(rand(100000,999999)),
                'google_id' => $socialUser->id
            ]);
        }

        Auth::guard('customer')->login($user);
        return redirect('/customer');
    }
//    public function getGoogleSignInUrl()
//    {
//        try {
//            $url = Socialite::driver('google')->stateless()
//                ->redirect()->getTargetUrl();
//            return response()->json([
//                'url' => $url,
//            ])->setStatusCode(Response::HTTP_OK);
//        } catch (\Exception $exception) {
//            return $exception;
//        }
//    }
//
//    public function loginCallback(Request $request)
//    {
//        try {
//            $state = $request->input('state');
//
//            parse_str($state, $result);
//            $googleUser = Socialite::driver('google')->stateless()->user();
//
//            $user = User::where('email', $googleUser->email)->first();
//            if ($user) {
//                throw new \Exception(__('google sign in email existed'));
//            }
//            $user = User::create(
//                [
//                    'email' => $googleUser->email,
//                    'name' => $googleUser->name,
//                    'google_id'=> $googleUser->id,
//                    'password'=> '123',
//                ]
//            );
//            return response()->json([
//                'status' => __('google sign in successful'),
//                'data' => $user,
//            ], Response::HTTP_CREATED);
//
//        } catch (\Exception $exception) {
//            return response()->json([
//                'status' => __('google sign in failed'),
//                'error' => $exception,
//                'message' => $exception->getMessage()
//            ], Response::HTTP_BAD_REQUEST);
//        }
//    }
}
