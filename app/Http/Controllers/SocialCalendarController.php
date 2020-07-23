<?php

namespace App\Http\Controllers;

use App\Mail\SocialCalendarPosts;
use App\Mail\TicketAutoResponse;
use App\SocialCalendar;
use App\SocialCalendarPost;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Webpatser\Uuid\Uuid;
use Yajra\DataTables\Html\Builder;
use DataTables;

class SocialCalendarController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Builder $builder, Request $request)
    {
        $user = auth()->user();
        if (request()->ajax()) {
            $calendar_query = SocialCalendar::query();

            if(!$user->admin) {
                $calendar_query->where('user_id',$user->id);
            }

            return DataTables::of($calendar_query->with('user')->get())
                ->addColumn('action', function ($calendar) use ($user) {
                    $html = "";
                    $html .= "<a class='btn btn-primary' href='".route('social-calendar.show',
                            $calendar->id)."'><span class='fa fa-eye'></span></a>";
                    if ($user->admin) {
                        $html .= "&nbsp;<a class='btn btn-warning' href='".route('social-calendar.edit',
                                $calendar->id)."'><span class='fa fa-edit'></span></a>";
                    }
                    return $html;
                })
                ->editColumn('approved_by_user', function ($calendar) {
                    $html = '';
                    if (is_null($calendar->approved_by_user)) {
                        $html = '<span class="badge badge-info">Pending</span>';
                    } elseif ($calendar->approved_by_user) {
                        $html = '<span class="badge badge-success">Approved</span>';
                    } else {
                        $html = '<span class="badge badge-danger">Denied</span>';
                    }
                    return $html;
                })
                ->rawColumns(['approved_by_user', 'action'])
                ->toJson();
        }
        $columns = [
            ['data' => 'id', 'name' => 'id', 'title' => 'ID'],
            ['data' => 'name', 'name' => 'name', 'title' => 'Name']
        ];

        if ($user->admin) {
            $columns[] = ['data' => 'user.name', 'name' => 'user.name', 'title' => 'Client'];
        }

        $columns = array_merge($columns, [
            ['data' => 'approved_by_user', 'name' => 'approved_by_user', 'title' => 'Approved?'],
            ['data' => 'action', 'name' => 'action', 'title' => '']
        ]);
        $html = $builder->columns($columns)
            ->parameters([
                'stateSave' => false,
                'searching' => true,
                'order' => [0, 'desc']
            ])
            ->ajax([
                // 'data' => 'function(d) { d.date_range = window.dateRange; }'
            ]);

        return view('social-calendar.index', compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('social-calendar.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'user_id' => 'required',
            'post.*' => 'required',
            'post.*.publish_date' => 'required|date',
            'post.*.post_url' => 'required|url',
            'post.*.post_publisher' => 'required|string',
            'post.*.post_topic' => 'required|string',
        ]);

        $calendar = new SocialCalendar([
            'name' => $request->input('name'),
            'user_id' => $request->input('user_id'),
            'calendar_id' => (string) Uuid::generate()
        ]);

        $calendar->save();

        foreach ($request->input('post') as $index => $post) {
            $calendar_post = new SocialCalendarPost([
                'calendar_id' => $calendar->id,
                'publish_date' => $post['publish_date'],
                'post_url' => $post['post_url'],
                'post_publisher' => $post['post_publisher'],
                'post_topic' => $post['post_topic'],
            ]);
            $calendar_post->save();
        }

        return redirect()->route('social-calendar.edit', $calendar->id)->with("status", "A Calendar has been created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SocialCalendar  $socialCalendar
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $socialCalendar = SocialCalendar::findOrFail($id);

        if (request()->ajax()) {
            $socialCalendar->load('posts')->get();
            return response()->json($socialCalendar);
        }
        return view('social-calendar.show', compact('socialCalendar'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\SocialCalendar  $socialCalendar
     * @return \Illuminate\Http\Response
     */
    public function edit(SocialCalendar $socialCalendar)
    {
        return view('social-calendar.edit', compact('socialCalendar'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\SocialCalendar  $socialCalendar
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, SocialCalendar $socialCalendar)
    {
        $this->validate($request, [
            'name' => 'required',
            'user_id' => 'required',
            'post.*' => 'required',
            'post.*.publish_date' => 'required|date',
            'post.*.post_url' => 'required|url',
            'editpost.*' => 'array',
            'editpost.*.publish_date' => 'date',
            'editpost.*.post_url' => 'url',
        ]);


        $socialCalendar->update([
            'name' => $request->input('name'),
            'user_id' => $request->input('user_id'),
        ]);

        if($request->has('editpost')) {
            foreach ($request->input('editpost') as $index => $post) {
                $calendar_post = SocialCalendarPost::find($post['id']);

                $calendar_post->update([
                    'publish_date' => $post['publish_date'],
                    'post_url' => $post['post_url'],
                    'post_publisher' => $post['post_publisher'],
                    'post_topic' => $post['post_topic'],
                    'admin_comment' => $post['admin_comment'],
                ]);
            }
        }

        if ($request->has('post')) {

            foreach ($request->input('post') as $index => $post) {
                $calendar_post = new SocialCalendarPost([
                    'calendar_id' => $socialCalendar->id,
                    'publish_date' => $post['publish_date'],
                    'post_url' => $post['post_url'],
                    'post_publisher' => $post['post_publisher'],
                    'post_topic' => $post['post_topic'],
                    'admin_comment' => $post['admin_comment'],
                ]);
                $calendar_post->save();
            }
        }

        if($request->has('submit_send')) {
            $socialCalendar->update(['approval_email_queued' => true]);

        }

        return redirect()->route('social-calendar.edit', $socialCalendar->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\SocialCalendar  $socialCalendar
     * @return \Illuminate\Http\Response
     */
    public function destroy(SocialCalendar $socialCalendar)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\SocialCalendar  $socialCalendar
     * @return \Illuminate\Http\Response
     */
    public function share($id)
    {
        $socialCalendar = SocialCalendar::where('calendar_id',$id)->firstOrFail();

        if (request()->ajax()) {
            $socialCalendar->load('posts')->get();
            return response()->json($socialCalendar);
        }
        return view('social-calendar.show', compact('socialCalendar'));
    }
    public function none()
    {
        return view('social-calendar.none');
    }
    public function redirect()
    {
        $socialCalendar = SocialCalendar::where('user_id',auth()->id())->orderBy('id','desc')->first();
        $routeData = [];

        if(request()->has('iframe')){
            $routeData['iframe'] = true;
        }
        if($socialCalendar) {
            $routeData['id'] = $socialCalendar->calendar_id;
            return redirect()->route('social-calendar.share', $routeData);
        }
        else {
            return redirect()->route('social-calendar.none',$routeData);
        }
    }
}
