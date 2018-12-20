<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $fillable = ['*'];

    public static function addUser($userId, $userName)
    {
    	$user = self::where('user_id', $userId)->get();
    	if ($user->count()) return true;
    	$user = new self;
    	$user->name = $userName;
    	$user->user_id = $userId;
    	$user->save();
    	return true;
    }
}
