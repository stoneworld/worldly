<?php

namespace App\Console\Commands;

use App\Console\Boot;
use Facades\ {
    App\Services\Netease
};
use Log;
use App\Models\User;
use App\Models\UserPlaylist;
use App\Models\UserMusic;
use Artisan;

class MusicPlayList extends Boot
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'netease:playlist {user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'netease playlist crawer';

    const MUSIC_URL = 'http://music.163.com/#/song?id=';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->start();
        $userId = $this->argument('user_id');
        $result = Netease::userPlaylists($userId); // 获取用户的歌单列表
        $userPlaylists = $this->getUserCreatePlayLists($result, $userId); // 处理歌单列表
        $this->insertUserPlayLists($userPlaylists, $userId); // 插入到数据库
        $this->crawerUserMusic($userId); // 爬取用户歌单中的所有歌曲并入库
        // 多进程爬取评论入库
        Artisan::queue('netease:comment', [
            'user_id' => $userId, '--mutix' => '--mutix',
        ]);
        $this->end();
    }

    /**
     * [getUserCreatePlayLists description]
     * @param  [type] $result [description]
     * @param  [type] $userId [description]
     * @return [type]         [description]
     */
    private function getUserCreatePlayLists($result, $userId)
    {
        $playlists = json_decode($result, true)['playlist'];

        if (!count($playlists)) {
            throw new \Exception("the user dont have any playlist", 1);
        }
        $collection = collect($playlists);
        $filteredList = $collection->filter(function ($playlist) use ($userId) {
            return $playlist['userId'] == $userId;
        });
        return $filteredList->all();
    }

    /**
     * [insertUserPlayLists description]
     * @param  [type] $userPlaylists [description]
     * @param  [type] $userId        [description]
     * @return [type]                [description]
     */
    private function insertUserPlayLists($userPlaylists, $userId)
    {
        $userName = $userPlaylists[0]['creator']['nickname'];
        User::addUser($userId, $userName);
        $userPlaylists = collect($userPlaylists)->transform(function ($playlist) {
            $list['playlist_id']    = $playlist['id'];
            $list['user_id']        = $playlist['userId'];
            $list['description']    = $playlist['description'];
            $list['name']           = $playlist['name'];
            return $list;
        });
        UserPlaylist::addOrUpdate($userPlaylists, $userId);
    }

    /**
     * [crawerUserMusic description]
     * @param  [type] $userId [description]
     * @return [type]         [description]
     */
    private function crawerUserMusic($userId)
    {
        $userPlayLists = UserPlaylist::where('user_id', $userId)->get()->pluck('playlist_id');
        $music = [];
        foreach ($userPlayLists as $key => $playlist) {
            $tracks = json_decode(Netease::playListInfo($playlist), true)['playlist']['tracks'];
            $music[] = $tracks;
        }
        $music = collect($music)->collapse()->unique('id')->transform(function ($item) use ($userId) {
            $list['music_id']   = $item['id'];
            $list['user_id']    = $userId;
            $list['music_name'] = $item['name'];
            $list['singer']     = $item['ar'][0]['name'];
            $list['music_url']  = self::MUSIC_URL . $item['id'];
            return $list;
        })->values()->all();
        UserMusic::addOrUpdate($userId, $music);
    }
}
