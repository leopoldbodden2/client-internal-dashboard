<?php

use Illuminate\Http\Request;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/sms-receive', 'CdyneMessageController@apiReceiveMessage')->name('sms-receive');

Route::get('social-calendar/{id}', 'SocialCalendarController@show');
Route::put('social-calendar-post/{id}', 'SocialCalendarPostController@update');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::middleware('auth:api')->name('api.')->group(function(){
    Route::post('/visitors','HomeController@apiVisitors')->name('visitors');
    Route::get('/phone-calls','HomeController@apiPhoneCalls')->name('phone-calls');
    Route::get('/email-contacts','HomeController@apiEmailContacts')->name('email-contacts');
    Route::get('/text-messages','HomeController@apiTextMessages')->name('text-messages');
    Route::post('/referrers','HomeController@apiReferrers')->name('referrers');
    Route::post('/visitor-location','HomeController@apiVisitorLocation')->name('visitor-location');
    Route::get('/reviews','HomeController@apiReviews')->name('reviews');

    Route::post('/call-tracking-value/create','CallTrackingValueController@create')->name('call-tracking-value.create');

    Route::get('/user/login-token',function(Request $request){
        $logintoken = (string) Str::uuid();
        $user = $request->user();
        $user->update(['logintoken' => $logintoken]);
        return response()->json($logintoken);
    });

    Route::apiResources([
        'sms-message' => 'CdyneMessageController'
    ]);

    Route::get('sms-message/{sms_message}/send', 'CdyneMessageController@send')->name('sms-message.send');
});

Route::middleware(['admin','auth:api'])->name('admin.api.')->group(function(){
    Route::resources([
        'social-calendar' => 'SocialCalendarController',
        'social-calendar-post' => 'SocialCalendarPostController',
        'users' => 'UserController',
        'passport-clients' => 'PassportClientController'
    ]);
});
