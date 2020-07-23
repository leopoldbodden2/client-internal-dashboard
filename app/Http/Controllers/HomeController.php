<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Analytics;
use App\CallTrackingValue;
use App\MailgunForms;
use Carbon\Carbon;
use DataTables;
use Google_Client;
use Google_Service_MyBusiness;
use GuzzleHttp\Client as HttpClient;
use Spatie\Analytics\Period;
use Yajra\DataTables\Html\Builder;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('home');
    }

    public function visitors(){
        return view('analytics.visitors');
    }
    public function apiVisitors(Request $request){
        $user = $request->user();

        $this->validate($request,[
            'date_range' => 'required|integer'
        ]);

        $analytics = Analytics::setViewId($user->analytics)
                                ->fetchTotalVisitorsAndPageViews(Period::days($request->input('date_range')));
        $labels = $datasets = [];
        foreach($analytics as $visit){
            $labels[] = $visit['date']->format('m/d');
            $datasets[] = $visit['visitors'];
        }
        return response()->json(compact('labels','datasets'));
    }

    public function phone_calls(Builder $builder, Request $request){
        $columns = [
                    ['data' => 'name', 'name' => 'name', 'title' => 'Contact'],
                    ['data' => 'caller_number_format', 'name' => 'caller_number_format', 'title' => 'Contact #', 'class'=>'d-none'],
                    ['data' => 'source', 'name' => 'source', 'title' => 'Source'],
                    ['data' => 'called_at', 'name' => 'called_at', 'title' => 'Date'],
                    ['data' => 'talk_time', 'name' => 'talk_time', 'title' => 'Length'],
                    ['data' => 'cost_value', 'name' => 'cost_value', 'title' => 'Value'],
                ];
        if(request()->ajax()){
            $user = auth()->user();

            $this->validate($request,[
                'date_range' => 'integer'
            ]);
            $calls = collect([]);
            if ($user && $user->hasCtm()) {
                $date_range = request()->has('date_range')?request()->input('date_range'):7;
                $start_date = Carbon::now()->subDays($date_range)->format('Y-m-d');
                $end_date = Carbon::now()->format('Y-m-d');
                $per_page = $request->input('length');
                $page = intval($request->input('page'))+1;
                $url = "https://api.calltrackingmetrics.com/api/v1/accounts/$user->ctm_id/calls.json?start_date=$start_date&end_date=$end_date&per_page=$per_page&page=$page";
                $client = new HttpClient();
                $response = json_decode($client->request('GET', $url, ['auth' => $user->ctm_auth])->getBody(),true);
                $callscollection = collect($response['calls']);
                $calls = $callscollection->map(function ($item) {
                    $item['called_at'] =  new Carbon($item['called_at']);
                    $ctv = CallTrackingValue::where('call_id',$item['id'])->first();
                    $item['callvalueid'] = $ctv?$ctv->id:'';
                    $item['cost_value'] = $ctv?'$'.number_format($ctv->cost_value,2):'';
                    $item['cost_value_number'] = $ctv?(float)$ctv->cost_value:0;
                    $item['name'] = ucwords($item['name']);
                    return $item;
                });
            }
            // resets the request to 0 position because this bugs out our hacky logic to handle api requests directly via datatables
            $request->merge(array('start' => 0));
            return DataTables::collection($calls)
                    ->setTotalRecords($response['total_entries'])
                    ->editColumn('called_at', function($call){
                        return "<span class='d-none'>{$call['called_at']->timestamp}</span>".$call['called_at']->format('F d, Y');
                    })
                    ->editColumn('talk_time',function($call){
                        $hours = floor($call['talk_time'] / 3600);
                        $minutes = sprintf('%02d',floor(($call['talk_time'] / 60) % 60));
                        $seconds = sprintf('%02d',$call['talk_time'] % 60);
                        $talk_time = '';
                        $talk_time .= $hours>0?sprintf('%02d',$hours).':':'';
                        $talk_time .= "$minutes:$seconds";
                        return $talk_time;
                    })
                    ->editColumn('name',function($call){
                        return '<strong>'.$call['name'].'</strong><br>'.$call['caller_number_format'];
                    })
                    ->addColumn('action',function($call){
                        return "<button
                                    data-toggle='modal'
                                    data-target='#modal-callvalue'
                                    data-callvalueid='{$call['callvalueid']}'
                                    data-callid='{$call['id']}'
                                    data-costvalue='{$call['cost_value_number']}'
                                    data-calltitle='".ucwords(strtolower($call['name'])).' <br>'.$call['caller_number_format']."'
                                    class='btn text-black btn-link btn-lg'>
                                        <span class='far fa-edit'></span>
                                </button>";
                    })
                    ->rawColumns(['name', 'called_at', 'action'])
                    ->with('total_sum', '$'.number_format($calls->sum('cost_value_number'),2))
                    ->with('total_avg', '$'.number_format($calls->avg('cost_value_number'),2))
                    ->toJson();
        }
        $html = $builder->columns($columns)
                ->parameters([
                    'stateSave' => false,
                    'searching' => false,
                    'ordering' => false,
                    'order' => [2, 'desc']
                ])
                ->ajax([
                    'data' => 'function(d) { d.date_range = window.dateRange; d.page = $("#phone-calls").DataTable().page.info().page; }'
                ])
                ->addAction();

        return view('analytics.phone-calls',compact('html'));
    }

    public function email_contacts(Builder $builder, Request $request){
        if(request()->ajax()){
            $user = $request->user();

            $this->validate($request,[
                'date_range' => 'integer'
            ]);
            if($user->client){
                if($user->client->mailgun_url){
                    $date_range = request()->has('date_range')?request()->input('date_range'):7;
                    $start_date = Carbon::now()->subDays($date_range)->format('Y-m-d');
                    $end_date = Carbon::now()->addDay()->format('Y-m-d');

                    return DataTables::of(MailgunForms::whereBetween('created_at',[$start_date,$end_date])->where('domain',$user->client->mailgun_url)->get())
                            ->editColumn('message',function($formfill){
                                return nl2br(e($formfill->message));
                            })
                            ->editColumn('created_at',function($formfill){
                                return "<span class='d-none'>{$formfill->created_at->timestamp}</span>".$formfill->created_at->format('m-d-Y');
                            })
                            ->rawColumns(['message', 'created_at', 'action'])
                            ->toJson();
                }

            }
            return DataTables::of(collect([]))->toJson();
        }
        $html = $builder->columns([
                    ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
                    ['data' => 'message', 'name' => 'message', 'title' => 'Message'],
                    ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Date'],
                    ['data' => 'domain', 'name' => 'domain', 'title' => 'domain','class'=>'d-none'],
                ])
                ->parameters([
                    'stateSave' => false,
                    'searching' => false,
                    'order' => [2, 'desc']
                ])
                ->ajax([
                    'data' => 'function(d) { d.date_range = window.dateRange; }'
                ]);

        return view('analytics.email-contacts',compact('html'));
    }

    public function text_messages(){
        return view('analytics.text-messages');
    }
    public function apiTextMessages(){
        return response()->json();
    }

    public function referrers(){
        return view('analytics.referrers');
    }
    public function apiReferrers(Request $request){
        $user = $request->user();

        $this->validate($request,[
            'date_range' => 'integer'
        ]);

        $analytics = Analytics::setViewId($user->analytics)->fetchTopReferrers(Period::days($request->input('date_range')),10);

        $labels = $datasets = [];
        foreach($analytics as $referrer){
            $labels[] = ucwords(str_limit($referrer['url'], 50));
            $datasets[] = $referrer['pageViews'];
        }
        return response()->json(compact('labels','datasets'));
    }

    public function visitor_location(){
        return view('analytics.visitor-location');
    }
    public function apiVisitorLocation(Request $request){
        $user = $request->user();

        $this->validate($request,[
            'date_range' => 'integer'
        ]);

        $analytics = Analytics::setViewId($user->analytics)->performQuery(
            Period::days($request->input('date_range')),
            'ga:sessions',
            [
                'metrics' => 'ga:sessions',
                'dimensions' => 'ga:city, ga:region',
                'sort' => '-ga:sessions',
                'filters' => 'ga:country==United States',
                'max-results' => 10
            ]
        );
        $labels = $datasets = [];
        if($analytics->rows && count($analytics->rows)){
            foreach($analytics->rows as $visitor){
                if($visitor[0] != '(not set)' && $visitor[1] != '(not set)'){
                    $labels[] = str_limit($visitor[0].', '.$visitor[1], 50);
                    $datasets[] = $visitor[2];
                }
            }
            $labels = array_slice($labels,0,20);
            $datasets = array_slice($datasets,0,20);
        }
        return response()->json(compact('labels','datasets'));
    }

    public function reviews(Builder $builder, Request $request){
        if(request()->ajax()){
            $user = $request->user();

            $this->validate($request,[
                'date_range' => 'integer'
            ]);
            $calls = collect([]);
            if ($user && $user->hasCtm()) {
                $date_range = request()->has('date_range')?request()->input('date_range'):7;
                /*dd($dates);
                $id = $user->ctm_id;
                $auth = explode(',', $user->ctm_auth);
                $url = "https://api.calltrackingmetrics.com/api/v1/accounts/$id/calls.json?start_date=$date_range";

                if(request()->has('date_range')) dd($url);
                $client = new HttpClient();
                $response = json_decode($client->request('GET', $url, ['auth' => [$auth[0], $auth[1]]])->getBody(),true);
                $calls = collect($response['calls']);
                $calls = $calls->map(function ($item) {
                    $called_at = new Carbon($item['called_at']);
                    $item['called_at'] = $called_at->format('F,d,Y');
                    $ctv = CallTrackingValue::where('call_id',$item['id'])->first();
                    $item['callvalueid'] = $ctv?$ctv->id:'';
                    $item['cost_value'] = $ctv?'$'.number_format($ctv->cost_value,2):'';
                    $item['cost_value_number'] = $ctv?(float)$ctv->cost_value:0;
                    $item['name'] = ucwords($item['name']);
                    return $item;
                });
                $analytics_service = Analytics::setViewId($user->analytics);
                $google_service = $analytics_service->getAnalyticsService();
                // returns a Guzzle HTTP Client
                $httpClient = $google_service->authorize();
                dd($httpClient);
                $response = $httpClient->get("https://mybusiness.googleapis.com/v4/accounts/$account_name/locations/$location_name/reviews");*/
                // create the Google client
                $client = new Google_Client();

                $client->setScopes([
                    'https://www.googleapis.com/auth/plus.business.manage'
                ]);


                $client->setAuthConfig(config('analytics.service_account_credentials_json'));

                /**
                 * Set your method for authentication. Depending on the API, This could be
                 * directly with an access token, API key, or (recommended) using
                 * Application Default Credentials.
                 *
                 * $account_name = 'ChIJG1bIgCTD5ogR3st-fZxqdkI';
                 * $location_name = 'ChIJG1bIgCTD5ogR3st-fZxqdkI';
                 * dd($httpClient->get("https://mybusiness.googleapis.com/v4/accounts/$account_name/locations/$location_name/reviews"));
                 */

                // returns a Guzzle HTTP Client
                $httpClient = $client->authorize();
                dd($httpClient->get("https://mybusiness.googleapis.com/v4/accounts"));
                // make an HTTP request
            }
            return DataTables::of($calls)
                    ->addColumn('action',function($call){
                        return "<a href='#modal-callvalue'
                                    data-toggle='modal'
                                    data-callvalueid='{$call['callvalueid']}'
                                    data-callid='{$call['id']}'
                                    data-costvalue='{$call['cost_value_number']}'
                                    data-calltitle='".ucwords($call['name'].' '.$call['caller_number_format'])."'
                                    class='btn text-black btn-link btn-sm'>
                                        <span class='far fa-edit'></span>
                                </a>";
                    })
                    ->with('total_sum', '$'.number_format($calls->sum('cost_value_number'),2))
                    ->with('total_avg', '$'.number_format($calls->avg('cost_value_number'),2))
                    ->make(true);
        }
        $html = $builder->columns([
                    ['data' => 'name', 'name' => 'name', 'title' => 'Contact'],
                    ['data' => 'caller_number_format', 'name' => 'caller_number_format', 'title' => 'Contact #'],
                    ['data' => 'source', 'name' => 'source', 'title' => 'Source'],
                    ['data' => 'called_at', 'name' => 'called_at', 'title' => 'Date'],
                    ['data' => 'talk_time', 'name' => 'talk_time', 'title' => 'Length'],
                    ['data' => 'cost_value', 'name' => 'cost_value', 'title' => 'Value'],
                ])
                ->parameters([
                    'stateSave' => true,
                    'searching' => false
                ])
                ->ajax([
                    'data' => 'function(d) { d.date_range = window.dateRange; }'
                ])
                ->addAction();

        return view('analytics.reviews',compact('html'));
    }

}
