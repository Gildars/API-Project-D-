<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MailConfirmation extends Model
{

    protected $table = 'mail_confirmation';

    protected $fillable
        = [
            'email',
            'token',
        ];
}
