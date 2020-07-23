<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class CallTrackingValue extends Model
{
    protected $fillable = ['call_id','cost_value','user_id'];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
