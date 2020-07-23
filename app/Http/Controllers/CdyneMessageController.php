<?php

namespace App\Http\Controllers;

use App\CdyneMessage;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Html\Builder;
use DataTables;
use Carbon\Carbon;

class CdyneMessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, Builder $builder)
    {
        if($request->ajax()){
            if(request()->has('date_range')){
                $this->validate($request,[
                    'date_range' => 'integer'
                ]);
                $date_range = request()->has('date_range')?request()->input('date_range'):7;

                $start_date = now()->subDays($date_range)->timestamp;
                $end_date = now()->addDay()->timestamp;

                $unread_only = false;

                $incoming_messages = $sms_message->ReadIncomingMessages($start_date->timestamp, $end_date->timestamp, $unread_only);
            }

            $user_id = auth()->id();
            $sms_query = CdyneMessage::where('user_id', $user_id);
            return DataTables::of($sms_query)
                    ->addColumn('action',function($user){
                        return "<button
                                    onclick='editCdyneMessage({$user->id})'
                                    class='btn text-black btn-link btn-lg' title='Edit Message'>
                                        <span class='far fa-edit'></span>
                                </button>
                                <button
                                    onclick='sendCdyneMessage({$user->id})'
                                    class='btn text-green btn-link btn-lg' title='Re-Send Message'>
                                        <span class='far fa-paper-plane'></span>
                                </button>";
                    })
                    ->toJson();
        }
        $html = $builder->columns([
                    ['data' => 'subject', 'name' => 'subject', 'title' => 'Subject'],
                    ['data' => 'body', 'name' => 'body', 'title' => 'Message'],
                    ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
                    ['data' => 'phone', 'name' => 'phone', 'title' => 'Phone'],
                ])
                ->ajax([
                    'url' => route('sms-message.index'),
                    'type' => 'GET',
                    'data' => 'function(d) { d.date_range = document.getElementById("dateRange").value; }',
                ])
                ->parameters([
                    'stateSave' => true,
                    'searching' => true,
                    'ordering' => true
                ])
                ->addAction();

        return view('cdyne-messages.index', compact('html'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validData = $this->validate($request, [
            'name' => 'nullable',
            'subject' => 'required|string',
            'body' => 'required|string',
            'phone' => 'required|phone'
        ]);

        $validData['user_id'] = auth()->id();
        $sms_message = new CdyneMessage;
        $sms_message->fill($validData);
        $sms_message->save();

        return response()->json($sms_message);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\CdyneMessage  $sms_message
     * @return \Illuminate\Http\Response
     */
    public function show(CdyneMessage $sms_message)
    {
        if(request()->ajax()){
            return response()->json($sms_message);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\CdyneMessage  $sms_message
     * @return \Illuminate\Http\Response
     */
    public function edit(CdyneMessage $sms_message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\CdyneMessage  $sms_message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, CdyneMessage $sms_message)
    {
        $validData = $this->validate($request, [
            'name' => 'nullable',
            'subject' => 'required|string',
            'body' => 'required|string',
            'phone' => 'required|phone'
        ]);

        $sms_message->fill($validData);
        $sms_message->save();

        return response()->json($sms_message);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\CdyneMessage  $sms_message
     * @return \Illuminate\Http\Response
     */
    public function destroy(CdyneMessage $sms_message)
    {
        $sms_message->delete();
        return response()->json(['success' => 'true']);
    }

    /**
     * Send the message via cdyne.
     *
     * @param  \App\CdyneMessage  $sms_message
     * @return \Illuminate\Http\Response
     */
    public function send(CdyneMessage $sms_message)
    {

        $messageResponse = $sms_message->SendMessage();
        if(request()->ajax()){
            return response()->json(compact('messageResponse', 'sms_message'));
        }
    }

    public function apiReceiveMessage(Request $request){
        Log::info($request->all());
    }

}
