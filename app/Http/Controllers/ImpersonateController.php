<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;

class ImpersonateController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
       $this->middleware('admin')->only('impersonate');
    }

    public function impersonate($id) {
        Auth::user()->impersonate(User::findOrFail($id));
        // You're now logged as the $other_user
        return redirect()->route('visitors');
    }

    public function impersonate_leave() {
        Auth::user()->leaveImpersonation();
        // You're now logged as your original user
        return redirect()->route('users.index');
    }

}
