<?php

namespace App\Http\Controllers;

use App\MailgunForms;
use App\Ticket;
use App\TicketCategory;
use App\Mail\TicketAutoResponse;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Yajra\DataTables\Html\Builder;
use DataTables;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Builder $builder, Request $request)
    {
        if(request()->ajax()){
            $user = auth()->user();

            if($user->admin) {
                $ticket_query = Ticket::query();
            }
            else {
                $ticket_query = Ticket::where('user_id', $user->id);
            }

            if($request->input('date_range') === 'ALL') {
                // do nothing
            }
            else {
                $date_range = request()->has('date_range')?request()->input('date_range'):7;
                $start_date = Carbon::now()->subDays($date_range)->format('Y-m-d');
                $end_date = Carbon::now()->addDay()->format('Y-m-d');

                $ticket_query->whereBetween('created_at',[$start_date,$end_date]);
            }

            return DataTables::of($ticket_query->get())
                ->editColumn('message',function($ticket){
                    return nl2br(e($ticket->message));
                })
                ->editColumn('created_at',function($ticket){
                    return "<span class='d-none'>{$ticket->created_at->timestamp}</span>".$ticket->created_at->format('m-d-Y');
                })
                ->addColumn('action', function($ticket) {
                    return "<a class='btn btn-primary' href='".route('ticket.show',$ticket->ticket_id)."'><span class='fa fa-eye'></span></a>";
                })
                ->editColumn('status', function($ticket) {
                    if ($ticket->status === 'Open'){
                        $html = '<span class="badge badge-success">';
                    }
                    else{
                        $html = '<span class="badge badge-danger">';
                    }
                    return $html.$ticket->status.'</span>';
                })
                ->rawColumns(['message', 'status', 'created_at', 'action'])
                ->toJson();
        }
            $html = $builder->columns([
                ['data' => 'message', 'name' => 'message', 'title' => 'Message'],
                ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
                ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Date'],
                ['data' => 'action', 'name' => 'action', 'title' => ''],
            ])
            ->parameters([
                'stateSave' => false,
                'searching' => false,
                'order' => [0, 'desc']
            ])
            ->ajax([
                'data' => 'function(d) { d.date_range = window.dateRange; }'
            ]);

        return view('tickets.index',compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = TicketCategory::all();

        return view('tickets.create',compact('categories'));
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
            'title'     => 'required',
            'category'  => 'required',
            'priority'  => 'required',
            'message'   => 'required'
        ]);

        $user = auth()->user();

        $ticket = new Ticket([
            'title'     => $request->input('title'),
            'user_id'   => $user->id,
            'ticket_id' => strtoupper(str_random(10)),
            'category_id'  => $request->input('category'),
            'priority'  => $request->input('priority'),
            'message'   => $request->input('message'),
            'status'    => "Open",
        ]);

        $ticket->save();

        Mail::to($user)->send(new TicketAutoResponse($user, $ticket));

        return redirect()->back()->with("status", "A ticket with ID: #$ticket->ticket_id has been opened.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id, Builder $builder, Request $request)
    {
        $user = auth()->user();
        $ticket = Ticket::where('ticket_id', $id)->firstOrFail();

        if(request()->ajax()){

            return DataTables::of(TicketComment::with('user')->where('ticket_id', $ticket->id)->get())
                ->editColumn('message',function($ticket){
                    return nl2br(e($ticket->message));
                })
                ->editColumn('created_at',function($ticket){
                    return "<span class='d-none'>{$ticket->created_at->timestamp}</span>".$ticket->created_at->format('m-d-Y');
                })
                ->addColumn('action', function($ticket) {
                    return "<a class='btn btn-primary' href='".route('ticket.show',$ticket->ticket_id)."'><span class='fa fa-eye'></span></a>";
                })
                ->editColumn('status', function($ticket) {
                    if ($ticket->status === 'Open'){
                        $html = '<span class="badge badge-success">';
                    }
                    else{
                        $html = '<span class="badge badge-danger">';
                    }
                    return $html.$ticket->status.'</span>';
                })
                ->rawColumns(['message', 'status', 'created_at', 'action'])
                ->toJson();
        }
        $html = $builder->columns([
            ['data' => 'message', 'name' => 'message', 'title' => 'Message'],
            ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Date'],
            ['data' => 'action', 'name' => 'action', 'title' => ''],
        ])
            ->parameters([
                'stateSave' => false,
                'searching' => false,
                'order' => [0, 'desc']
            ])
            ->ajax([
                'data' => 'function(d) { d.id = '.$id.'; }'
            ]);

        return view('tickets.show',compact('ticket','user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
