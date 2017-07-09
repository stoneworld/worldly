<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPlaylist extends Model
{
    protected $fillable = ['*'];

    public static function addOrUpdate($userPlaylists, $userId)
    {
    	$userExistPlaylist = self::where('user_id', $userId)->get()->pluck('playlist_id')->toArray();
    	$data = collect($userPlaylists)->filter(function ($playlist) use ($userExistPlaylist) {
    		return !in_array($playlist['playlist_id'], $userExistPlaylist);
    	})->values()->all();
    	self::insert($data);
    }
}
