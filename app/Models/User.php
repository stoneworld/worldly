<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
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
