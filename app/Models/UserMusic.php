<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMusic extends Model
{
    protected $fillable = ['*'];

    protected $table = 'user_music';
    public $timestamps = false;
    public static function addOrUpdate($userId, $music)
    {
    	$userExistMusic = self::where('user_id', $userId)->get()->pluck('music_id')->toArray();
    	$data = collect($music)->filter(function ($item) use ($userExistMusic) {
    		return !in_array($item['music_id'], $userExistMusic);
    	})->values()->all();
    	self::insert($data);
    }
}
