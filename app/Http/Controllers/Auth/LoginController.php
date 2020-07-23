<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\User;
use Auth;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout','wordpress_login');
    }

    public function wordpress_login(Request $request){
        if(auth()->check()){
            auth()->user()->update(['logintoken' => null]);
        }
        else{
            $user = User::where('email',$request->input('email'))->where('logintoken',$request->input('logintoken'))->first();
            if($user){
                Auth::login($user, true);
                $user->update(['logintoken' => null]);
            }
        }
        session()->put('iframe','iframe');
        return redirect()->away($request->input('redirect_uri'));
    }
}
