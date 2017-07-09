<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserComment extends Model
{
    protected $fillable = ['*'];


    public static function addOrUpdate($userComments, $userId)
    {
    	$userExistComments = self::where('user_id', $userId)->get()->pluck('comment_id')->toArray();
    	$data = collect($userComments)->filter(function ($item) use ($userExistComments) {
    		return !in_array($item['comment_id'], $userExistComments);
    	})->values()->all();
    	self::insert($data);
    }
}
