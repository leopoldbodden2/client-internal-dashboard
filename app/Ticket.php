<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'category_id',
        'ticket_id',
        'title',
        'priority',
        'message',
        'status'
    ];

    /**
     * The type casts for the attributes
     *
     * @var array
     */
    protected $casts = [
        'user_id' => 'integer',
        'category_id' => 'integer',
        'ticket_id' => 'string',
        'title' => 'string',
        'priority' => 'string',
        'message' => 'string',
        'status' => 'string'
    ];

    public function category(){
        return $this->belongsTo(TicketCategory::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
