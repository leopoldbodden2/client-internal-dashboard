<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $connection = 'timesheets-portal';

    protected $casts = [
        'mailgun_url' => 'string'
    ];

    public function user(){
        return $this->hasOne(User::class);
    }
}
