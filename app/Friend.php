<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Friend extends Model
{
    protected $table = 'friend';

    protected $fillable = [
        'id',
        'id_friend_one',
        'id_friend_two'
    ];

    public $timestamps = false;

}
