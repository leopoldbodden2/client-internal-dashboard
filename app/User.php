<?php

namespace App;

use Analytics;
use Spatie\Analytics\Period;
use App\Client;
use Carbon\Carbon;
use GuzzleHttp\Client as HttpClient;
use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Lab404\Impersonate\Models\Impersonate;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    use Impersonate;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'analytics',
        'ctm_id',
        'ctm_auth',
        'mobile_id',
        'client_id',
        'logintoken',
        'admin',
        'cdyne_phone',
        'cdyne_license_key'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'analytics',
        'ctm_id',
        'ctm_auth',
        'mobile_id',
        'client_id',
        'logintoken',
        'admin'
    ];

    /**
     * The type casts for the attributes
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'email' => 'string',
        'analytics' => 'string',
        'ctm_id' => 'string',
        'mobile_id' => 'string',
        'client_id' => 'integer',
        'admin' => 'boolean'
    ];

    public function sms_messages(){
        return $this->hasMany(CdyneMessage::class);
    }

    public function getClientAttribute(){
        return Client::where('id',$this->client_id)->first();
    }

    public function getDisplayNameAttribute(){
        if($this->client){
            if($this->client->main_url){
                return $this->client->main_url;
            }
            return $this->client->name;
        }
        return $this->name;
    }

	/**
	 * Boolean indicating whether the user has a call tracking metrics account setup
	 *
	 * @return bool
	 */
    public function hasCtm() {
    	return $this->ctm_id !== null;
    }

	/**
	 * Boolean indicating whether the user has a mailgun account setup
	 *
	 * @return bool
	 */
    public function hasMailgun() {
    	return $this->client ?? $this->client->mailgun_url;
    }

    public function getCtmAuthAttribute($value){
        return explode(',',$value);
    }
    public function getSiteStatsAttribute(){
        $stats_period = 30;
        $organicsources = Analytics::setViewId($this->analytics)->performQuery(
                        Period::days($stats_period),
                        'ga:source',
                        [
                            'metrics' => 'ga:organicSearches'
                        ]
                    )->rows;
        if(is_array($organicsources)){
            $googlesources = $organicsources[0][0];
        }
        else{
            $googlesources = 0;
        }

        $visits = Analytics::setViewId($this->analytics)->fetchTotalVisitorsAndPageViews(Period::days($stats_period))->sum('visitors');
        $popular_pages = Analytics::setViewId($this->analytics)->fetchMostVisitedPages(Period::days($stats_period),5);
        $referrers = Analytics::setViewId($this->analytics)->fetchTopReferrers(Period::days($stats_period));

        $locationAnalytics = Analytics::setViewId($this->analytics)->performQuery(
            Period::days($stats_period),
            'ga:sessions',
            [
                'metrics' => 'ga:sessions',
                'dimensions' => 'ga:city, ga:region',
                'sort' => '-ga:sessions',
                'filters' => 'ga:country==United States',
                'max-results' => 5
            ]
        );
        $locations = collect([]);
        if($locationAnalytics->rows && count($locationAnalytics->rows)){
            foreach($locationAnalytics->rows as $visitor){
                if($visitor[0] != '(not set)' && $visitor[1] != '(not set)'){
                    $locations->push([
                        'name' => $visitor[0].', '.$visitor[1],
                        'visitors' => $visitor[2]
                    ]);
                }
            }
        }

        $emails = 0;

        if($this->client){
            if($this->client->mailgun_url){
                $start_date = Carbon::now()->subDays($stats_period)->format('Y-m-d');
                $end_date = Carbon::now()->addDay()->format('Y-m-d');
                $emails = MailgunForms::whereBetween('created_at',[$start_date,$end_date])->where('domain',$this->client->mailgun_url)->count();
            }
        }
        return compact('visits','popular_pages','referrers','googlesources','locations', 'emails');
    }
    public function getPhoneStatsAttribute(){
        $total = 0;
        if ($this->hasCtm()) {
            $stats_period = 30;
            $start_date = Carbon::now()->subDays($stats_period)->format('Y-m-d');
            $end_date = Carbon::now()->format('Y-m-d');
            $url = "https://api.calltrackingmetrics.com/api/v1/accounts/$this->ctm_id/calls.json?start_date=$start_date&end_date=$end_date";
            $client = new HttpClient();
            $response = json_decode($client->request('GET', $url, ['auth' => $this->ctm_auth])->getBody(),true);
            $total = count($response['calls']);
        }
        return compact('total');
    }

    /**
     * @return bool
     */
    public function canImpersonate()
    {
        return $this->admin;
    }

    /**
     * @return bool
     */
    public function canBeImpersonated()
    {
        return !$this->admin;
    }
}
