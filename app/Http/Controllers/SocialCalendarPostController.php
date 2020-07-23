<?php

namespace App\Http\Controllers;

use App\SocialCalendar;
use App\SocialCalendarPost;
use Illuminate\Http\Request;

class SocialCalendarPostController extends Controller
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
        $socialCalendarPost = SocialCalendarPost::findOrFail($id);

        $validData = $this->validate($request,[
            'user_approved' => 'required|boolean'
        ]);

        $socialCalendarPost->update([
            'client_comment' => $request->input('client_comment'),
            'user_approved' => $request->input('user_approved')
        ]);

        if($request->ajax()){
            return response()->json(['success'=>true]);
        }
        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(SocialCalendarPost $socialCalendarPost)
    {
        $socialCalendarPost->delete();

        return response()->json(['success' => 'true']);
    }
}
