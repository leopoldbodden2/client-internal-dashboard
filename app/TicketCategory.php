<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TicketCategory extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The type casts for the attributes
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string'
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
