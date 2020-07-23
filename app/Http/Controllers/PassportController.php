<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PassportController extends Controller
{

    /**
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function authorized_clients()
    {
        return view('passport.authorized-clients');
    }

    /**
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function clients()
    {
        return view('passport.clients');
    }

    /**
     *
     *
     * @return \Illuminate\Http\Response
     */
    public function personal_access_tokens()
    {
        return view('passport.personal-access-tokens');
    }
}
