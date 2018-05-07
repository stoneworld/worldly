<?php

namespace App\Console\Commands;

use App\Console\Boot;
use App\Models\UserMusic;
use App\Models\UserComment;
use Facades\ {
    App\Services\Netease
};
use Log;

class MusicComment extends Boot
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'netease:comment {user_id} {--mutix} {--limit=} {--offset=}  {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'netease comment';

    protected $userId;

    /**
     * Create a new command instance.
     *
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
        if ($this->option('mutix')) {
            $this->mutix();
        } else {
            $this->grap();
        }
        $this->end();
    }

    /**
     * mutix
     */
    public function mutix()
    {
        $userId = $this->argument('user_id');
        $count = UserMusic::where('user_id', $userId)->count();
        $this->scryed($count,50,[base_path() .'/artisan','netease:comment', $userId]);
    }

    public function grap()
    {
        $offset = $this->option('offset') ? : 0;
        $limit = $this->option('limit') ? : 0;
        $query = UserMusic::where('user_id', $this->argument('user_id'))
                            ->where('status', 0);
        $userMusic = $query->skip($offset)->take($limit)->get();
        if ($userMusic->count()) {
            foreach ($userMusic as $key => $music) {
                $commentCount = Netease::songComment($music->music_id, 0)['total']; // 获取歌曲总评论数量
                $this->info('music comment count'. $commentCount);
                $this->crawerMusicComment($music->music_id, $commentCount);
            }
        }

    }

    /**
     * @param $musicId
     * @param $count
     */
    private function crawerMusicComment($musicId, $count)
    {
        $userId = $this->argument('user_id');
        $pageNums = (int)ceil($count / 100);
        $userComments= [];
        for ($i=0; $i <= $pageNums; $i++) {
            $comments = Netease::songComment($musicId, $i*100)['comments'];
            if (!count($comments)) {
                continue;
            }
            $filter = collect($comments)->filter(function ($comment) use ($userId) {
                return $comment['user']['userId'] == $userId;
            })->transform(function ($comment) use ($musicId) {
                $list['user_id']        = $comment['user']['userId'];
                $list['content']        = $comment['content'];
                $list['comment_id']     = $comment['commentId'];
                $list['liked_count']    = $comment['likedCount'];
                $list['music_id']       = $musicId;
                $list['created_at']     = date("Y-m-d H:i:s", substr($comment['time'], 0, 10));
                return $list;
            })->values()->collapse()->all();
            if (!empty($filter)) {
                $userComments[] = $filter;
            }
        }
        $music = UserMusic::where("user_id", $userId)->where("music_id", $musicId)->first();
        $music->status = 1;
        $music->save();
        UserComment::addOrUpdate($userComments, $userId);
    }
}
