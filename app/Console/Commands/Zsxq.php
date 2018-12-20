<?php

namespace App\Console\Commands;

use App\Models\Xiaomiquan\Group;
use App\Models\Xiaomiquan\Reply;
use App\Models\Xiaomiquan\Topic;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class Zsxq extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'zsxq:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private $zsxq;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->zsxq = new \App\Services\Zsxq();
    }

    public $groupIds = [
        ''
    ];

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \Exception
     */
    public function handle()
    {
        $time = 0;
        while (true) {
            foreach ($this->groupIds as $groupId) {
                $group = Group::find($groupId);
                if (!empty($groupInfo)) {
                    try {
                        $group = $this->addNewGroup($groupId);
                    } catch (\Exception $exception) {
                        Log::error($exception->getMessage());
                        $group = null;
                    }
                }
                $this->getPublicTopicByGroupId($groupId, $group);
            }
            sleep(100);
            echo "{$time}次成功完成拉取\n";
            $time++;
        }
    }

    private function addNewGroup($groupId)
    {
        $groupInfo = $this->zsxq->getGroupPublicInfo($groupId);
        if ($groupInfo['succeeded'] == false) {
            ding()->at([""],false)->text('登录失效!');
            throw new \Exception('获取信息失败');
        }
        $groupInfo = $groupInfo['resp_data']['public_info'];
        $data['id'] = $groupInfo['group_id'];
        $data['number'] = $groupInfo['number'];
        $data['name'] = $groupInfo['name'];
        $data['description'] = $groupInfo['description'];
        $data['owner_user_id'] = $groupInfo['owner']['owner_user_id'];
        $data['create_time'] = $groupInfo['create_time'];
        $data['background_url'] = $groupInfo['background_url'];
        $data['type'] = $groupInfo['type'];
        $group = Group::create($data);
        return $group;
    }

    private function getPublicTopicByGroupId($groupId, $group)
    {
        $responseData = $this->zsxq->getPublicTopicsByGroupId($groupId);
        if ($responseData['succeeded'] == false) {
            ding()->at([""],false)->text('抓取失败，赶紧看日志去吧!');
            Log::notice($responseData);
            exit;
            throw new \Exception('获取信息失败');
        }
        $publicTopics = $responseData['resp_data']['public_topics'];
        if (empty($publicTopics)) return;
        if (empty($group)) {
            $data['id'] = $publicTopics[0]['group']['group_id'];
            $data['name'] = $publicTopics[0]['group']['name'];
            $group = Group::create($data);
        }
        foreach ($publicTopics as $publicTopic) {
            $topicId = $publicTopic['topic_id'];
            $type = $publicTopic['type'];
            $topic = Topic::find($topicId);
            if (!empty($topic)) continue;
            $data = [];
            if ($type == Topic::TYPE_QA) {
                if (!isset($publicTopic['answer']) || empty($publicTopic['answer']['text'])) continue;
            }
            $data['type'] = $type;
            $data['id'] = $topicId;
            $data['group_id'] = $groupId;
            $data['from_user_id'] = $type;
            $data['type'] = $type;
            $data['reading_count'] = $publicTopic['reading_count'];
            $data['likes_count'] = $publicTopic['likes_count'];
            $data['create_time'] = $publicTopic['create_time'];
            $data['desc'] = isset($publicTopic['talk']['text'])?$publicTopic['talk']['text']:$publicTopic['question']['text'];
            if ($type == Topic::TYPE_QA && $publicTopic['question']['anonymous']) {
                $data['from_user_name'] = '匿名用户';
                $data['from_user_id'] = 0;
            }else {
                $data['from_user_name'] = isset($publicTopic['talk']['owner']['name'])?$publicTopic['talk']['owner']['name']:$publicTopic['question']['owner']['name'];
                $data['from_user_id'] = isset($publicTopic['talk']['owner']['user_id'])?$publicTopic['talk']['owner']['user_id']:$publicTopic['question']['owner']['user_id'];
                $images = isset($publicTopic['talk']['images'])?$publicTopic['talk']['images']:[];
                $imageList = [];
                foreach ($images as $image) {
                    $imageList[] = $image['thumbnail']['url'];
                }
                $data['images'] = !empty($imageList)?json_encode($imageList, true):null;
            }
            $data['answer_text'] = isset($publicTopic['answer'])?$publicTopic['answer']['text']:'';
            $data['answer_user_id'] = isset($publicTopic['answer'])?$publicTopic['answer']['owner']['user_id']:0;
            $data['answer_user_name'] = isset($publicTopic['answer'])?$publicTopic['answer']['owner']['name']:'';
            Topic::create($data);
            if (isset($publicTopic['show_comments']) && !empty($publicTopic['show_comments'])) {
                $showComments = $publicTopic['show_comments'];
                $comments = [];
                foreach ($showComments as $comment) {
                    $comments[] = [
                        'id' => $comment['comment_id'],
                        'topic_id' => $topicId,
                        'text' => $comment['text'],
                        'likes_count' => $comment['likes_count'],
                        'rewards_count' => $comment['rewards_count'],
                        'create_time' => $comment['create_time'],
                        'user_id' => $comment['owner']['user_id'],
                        'user_name' => $comment['owner']['name'],
                    ];
                }
                Reply::insert($comments);
            }
            $this->ding($topicId, $type, $group);
        }
    }

    private function ding($topicId, $type,$group)
    {
        $topic = Topic::find($topicId);
        if ($group['id'] == '552522111144') $group['name'] = '数字货币价值投资1';
        $markdown = '#### 圈子名称：'.$group['name']. "\n";
        $markdown .= "> 类型：{$topic['type']}\n\n";
        if ($type == Topic::TYPE_QA) {
            $markdown .= "> 问题：{$topic['desc']}, create_time : {$topic['create_time']}\n\n";
            $markdown .= "> 回答人：{$topic['answer_user_name']} \n\n";
            $markdown .= "> 答案：{$topic['answer_text']}\n\n";
        } else {
            $markdown .= "> 描述：{$topic['from_user_name']} ： {$topic['desc']}, create_time : {$topic['create_time']}\n\n";
            if (!empty($topic['images'])) {
                $images = json_decode($topic['images'], true);
                foreach ($images as $image) {
                    $markdown .= "> 图片：![相关图片]({$image})\n\n";
                }
            }
        }
        ding()->markdown($group['name'],$markdown);
    }
}
