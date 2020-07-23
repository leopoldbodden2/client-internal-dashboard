<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    if(request()->has('iframe')){
        session()->put('iframe','iframe');
    }
    if(auth()->check()){
        return redirect()->route('visitors');
    }
    else{
        return redirect('/login');
    }
});
Route::get('/home', function () {
    if(request()->has('iframe')){
        session()->put('iframe','iframe');
    }
    if(auth()->check()){
        if(request()->has('iframe')){
            session('iframe','iframe');
        }
        return redirect()->route('visitors');
    }
    else{
        return redirect('/login');
    }
})->name('home');

Auth::routes();
Route::get('/wordpress-login','Auth\LoginController@wordpress_login');
Route::get('/social-calendar/share/{id}','SocialCalendarController@share')->name('social-calendar.share');


Route::middleware(['auth'])->group(function() {
    Route::get('/impersonate/leave','ImpersonateController@impersonate_leave')->name('impersonate.leave');
    Route::get('/social-calendar/none', 'SocialCalendarController@none')->name('social-calendar.none');
    Route::get('/social-calendar-redirect', 'SocialCalendarController@redirect')->name('social-calendar.redirect');
});

Route::middleware(['auth','admin'])->group(function(){
    Route::get('/impersonate/{id}','ImpersonateController@impersonate')->name('impersonate');

    Route::get('/wireframe',function(){
        return view('wireframe');
    });
    Route::get('/mailable/{user_id}', function ($user_id) {
        $user = App\User::findOrFail($user_id);
        if($user){
            return new App\Mail\UserSitePerformance($user);
        }
        else{
            abort(404);
        }
    });
    Route::get('/authorized-clients','PassportController@authorized_clients')->name('authorized-clients');
    Route::get('/clients','PassportController@clients')->name('clients');
    Route::get('/personal-access-tokens','PassportController@personal_access_tokens')->name('personal-access-tokens');

    Route::resources([
        'call-tracking-value' => 'CallTrackingValueController',
        'users'               => 'UserController',
        'social-calendar' => 'SocialCalendarController'
    ]);
});

Route::middleware('auth')->group(function(){
    Route::get('/visitors','HomeController@visitors')->name('visitors');
    Route::get('/phone-calls','HomeController@phone_calls')->name('phone-calls');
    Route::get('/email-contacts','HomeController@email_contacts')->name('email-contacts');
    Route::get('/text-messages','HomeController@text_messages')->name('text-messages');
    Route::get('/referrers','HomeController@referrers')->name('referrers');
    Route::get('/visitor-location','HomeController@visitor_location')->name('visitor-location');
    Route::get('/reviews','HomeController@reviews')->name('reviews');

    Route::resources([
        'call-tracking-value' => 'CallTrackingValueController',
        'sms-message' => 'CdyneMessageController',
        'ticket' => 'TicketController'
    ]);
});
