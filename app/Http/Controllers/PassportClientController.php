<?php

namespace App\Http\Controllers;

use Laravel\Passport\Http\Controllers\ClientController;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\ClientRepository;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
class PassportClientController extends ClientController
{
    public function index(){
        return Passport::client()->orderBy('name', 'asc')->get()->makeVisible(['secret','password_client','user_id'])->toJson();
    }
    /**
     * Store a new client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validation->make($request->all(), [
            'name' => 'required|max:255',
            'redirect' => 'required|url',
            'password_client' => 'nullable|boolean',
            'user_id' => 'required|exists:users,id'
        ])->validate();
        return $this->clients->create($request->user_id, $request->name, $request->redirect, false, $request->password_client)->makeVisible(['secret','password_client','user_id']);
    }

    /**
     * Update the given client.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $clientId
     * @return \Illuminate\Http\Response|\Laravel\Passport\Client
     */
    public function update(Request $request, $clientId)
    {
        $this->validation->make($request->all(), [
            'name' => 'required|max:255',
            'redirect' => 'required|url',
            'password_client' => 'nullable|boolean',
            'user_id' => 'required|exists:users,id'
        ])->validate();

        $client = Passport::client()->where('id',$clientId)->firstOrFail();
        
        $client->forceFill([
            'user_id' => $request->user_id,
            'name' => $request->name,
            'redirect' => $request->redirect,
            'password_client' => $request->password_client
        ])->save();
        return response()->json($client);
    }
}
