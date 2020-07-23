<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialCalendar extends Model
{
    protected $fillable = [
        'name',
        'calendar_id',
        'user_id',
        'approval_email_queued',
        'approval_email_sent',
        'approval_email_read',
        'approved_by_user',
        'published',
        'published_at'
    ];

    protected $casts = [
        'name' => 'string',
        'calendar_id' => 'string',
        'user_id' => 'integer',
        'approval_email_queued' => 'boolean',
        'approval_email_sent' => 'boolean',
        'approval_email_read' => 'boolean',
        'approved_by_user' => 'boolean',
        'published' => 'boolean',
        'published_at' => 'date'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function posts() {
        return $this->hasMany(SocialCalendarPost::class,'calendar_id','id')->orderBy('publish_date','asc');
    }


    public function getApprovedByUserAttribute($value) {
        if(is_null($value)) {
            $total_count = $this->posts()->count();
            // $null_count = $this->posts->whereNull('user_approved')->count();
            $approved_count = $this->posts()->where('user_approved',1)->count();
            $denied_count = $this->posts()->where('user_approved',0)->count();

            if( $approved_count > 0 ) {
               $this->approved_by_user = $value = true;
               $this->save();
            }
            elseif($denied_count === $total_count) {
                $this->approved_by_user = $value = false;
                $this->save();
            }
        }
        return $value;
    }
}
