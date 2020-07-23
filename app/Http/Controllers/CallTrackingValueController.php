<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CallTrackingValue;

class CallTrackingValueController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $validData = $this->validate($request,[
            'id' => 'nullable',
            'call_id' => 'required',
            'cost_value' => 'nullable|numeric'
        ]);
        if($validData['id']){
            $callTrackingValue = CallTrackingValue::findOrFail($validData['id']);
            $callTrackingValue->update($validData);
        }
        else{
            $validData['user_id'] = $request->user()->id;
            $callTrackingValue = CallTrackingValue::create($validData);
        }
        if($request->ajax()){
            return response()->json(['success'=>true]);
        }
        return redirect()->back();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $callTrackingValue = CallTrackingValue::findOrFail($id);
        $validData = $this->validate($request,[
            'call_id' => 'required',
            'cost_value' => 'nullable|numeric'
        ]);
        $callTrackingValue->update($validData);
        return redirect()->back();
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
