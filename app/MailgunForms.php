<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailgunForms extends Model
{
    protected $fillable = ['domain', 'email', 'message'];
}
