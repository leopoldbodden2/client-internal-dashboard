<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SocialCalendarPost extends Model
{
    protected $fillable = [
        'calendar_id',
        'publish_date',
        'post_title',
        'post_url',
        'post_publisher',
        'post_topic',
        'user_approved',
        'client_comment',
        'admin_comment'
    ];

    protected $casts = [
        'calendar_id' => 'integer',
        'publish_date' => 'date|Y-m-d',
        'post_title' => 'string',
        'post_url' => 'string',
        'post_publisher' => 'string',
        'post_topic' => 'string',
        'user_approved' => 'boolean',
        'client_comment' => 'string',
        'admin_comment' => 'string'
    ];

    protected $appends = [
        'client_comment_html',
        'admin_comment_html'
    ];


    public function calendar() {
        return $this->belongsTo(SocialCalendar::class,'calendar_id','id');
    }

    public function getAdminCommentHtmlAttribute() {
        return nl2br(e($this->admin_comment));
    }

    public function getClientCommentHtmlAttribute() {
        return nl2br(e($this->client_comment));
    }
}
