<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property Character character
 * @property integer id
 */
class Friend extends Model
{
    protected $table = 'friends';

    protected $fillable = [
        'id',
        'id_friend_one',
        'id_friend_two'
    ];
}
