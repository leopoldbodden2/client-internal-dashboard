<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Html\Builder;
use DataTables;
use App\User;
use Illuminate\Validation\Rule;
use Hash;

class UserController extends Controller
{
    /**
     * Instantiate a new controller instance.
     *
     * @todo add admin roles
     * @todo add policies
     * @return void
     */
    public function __construct()
    {
       // $this->middleware('admin')->only('index');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Builder $builder, Request $request)
    {
        if(request()->ajax()){
            return DataTables::of(User::query())
                    ->addColumn('client_name',function($user){
                        $client = $user->client;
                        return $client?$client->name:'';
                    })
                    ->addColumn('action',function($user){
                        $html = "<a
                                    href='".route('impersonate',$user->id)."'
                                    class='btn text-black btn-link btn-lg'
                                    title='Login/Impersonate {$user->name}'>
                                        <span class='far fa-user-secret'></span>
                                </a>";

                        $html .= "<button
                                    onclick='editUser({$user->id})'
                                    class='btn text-black btn-link btn-lg'>
                                        <span class='far fa-edit'></span>
                                </button>";


                        return $html;
                    })
                    ->toJson();
        }
        $html = $builder->columns([
                    ['data' => 'name', 'name' => 'name', 'title' => 'Name'],
                    ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
                    ['data' => 'client_name', 'name' => 'client_name', 'title' => 'Client Name'],
                ])
                ->parameters([
                    'stateSave' => true,
                    'searching' => true,
                    'ordering' => true
                ])
                ->addAction();

        return view('users.index',compact('html'));
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
        $validData = $this->validate($request,[
            'name' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('users'),
            ],
            'analytics' => 'nullable|string',
            'ctm_id' => 'nullable|string',
            'ctm_auth' => 'nullable|string',
            'cdyne_phone' => 'nullable|string',
            'cdyne_license_key' => 'nullable|string',
            'mobile_id' => 'nullable|string',
            'client_id' => 'nullable|integer',
            'password' => 'required|string|min:6|confirmed',
            'admin' => 'nullable|boolean'
        ]);
        $validData['password'] = Hash::make($validData['password']);
        User::create($validData);

        if($request->ajax()){
            return response()->json(['success'=>true]);
        }
        return redirect()->back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if($request->ajax()){
            if(auth()->user()->admin){
                return response()->json($user->makeVisible(['analytics', 'ctm_id', 'ctm_auth', 'mobile_id', 'client_id', 'admin']));
            }
        }
        abort(404);
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
        $user = User::findOrFail($id);

        $validData = $this->validate($request,[
            'name' => 'required|string',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($id),
            ],
            'analytics' => 'nullable|string',
            'ctm_id' => 'nullable|string',
            'ctm_auth' => 'nullable|string',
            'cdyne_phone' => 'nullable|string',
            'cdyne_license_key' => 'nullable|string',
            'mobile_id' => 'nullable|string',
            'client_id' => 'nullable|integer',
            'password' => 'nullable|string|min:6|confirmed',
            'admin' => 'nullable|boolean'
        ]);
        if(is_null($validData['password'])){
            unset($validData['password']);
        }
        else{
            $validData['password'] = Hash::make($validData['password']);
        }
        $user->update($validData);

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
    public function destroy($id)
    {
        //
    }
}
