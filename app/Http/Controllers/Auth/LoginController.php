<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;

use Illuminate\Foundation\Auth\AuthenticatesUsers;


class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    //Google Login
public function redirectToGoogle(){
    return Socialite::driver('google')->redirect();
    }
    
    //Google callback  
    public function handleGoogleCallback(){
    
    $user = Socialite::driver('google')->redirect();
    
      $this->_registerorLoginUser($user);
      return redirect()->route('home');
    }
    
    //Facebook Login
    public function redirectToFacebook(){
    return Socialite::driver('facebook')->redirect();
    }
    
    //facebook callback  
    public function handleFacebookCallback(){
    
    $user = Socialite::driver('facebook')->redirect();
    
      $this->_registerorLoginUser($user);
      return redirect()->route('home');
    }
    
    //Github Login
    public function redirectToGithub(){
    return Socialite::driver('github')->redirect();
    }
    
    //github callback  
    public function handleGithubCallback(){
    
    $user = Socialite::driver('github')->redirect();
    
      $this->_registerorLoginUser($user);
      return redirect()->route('home');
    }
}
