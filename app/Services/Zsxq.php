<?php
/**
 * User: wangshuai
 * Date: 2018/11/9
 */

namespace App\Services;

class Zsxq
{
    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Zsxq constructor.
     */
    public function __construct()
    {
        $this->baseUrl = 'https://api.zsxq.com';
        $this->client = new \GuzzleHttp\Client(['base_uri' => $this->baseUrl]);
    }

    /**
     * 根据圈子id获取预览的消息内容
     * @param $groupId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPublicTopicsByGroupId($groupId)
    {
        $uri = '/v1.10/groups/'.$groupId.'/public_topics';
        return $this->requestGet($uri, true);
    }


    /**
     * 获取圈子的基本信息
     * @param $groupId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getGroupPublicInfo($groupId)
    {
        $uri = '/v1.10/groups/'.$groupId.'/public_info';
        return $this->requestGet($uri, true);
    }


    /**
     * get请求
     * @param $uri
     * @param bool $withHeader
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function requestGet($uri, $withHeader = false)
    {
        $header = [];
        if ($withHeader) {
            $header = [
                'headers' => [
                    'User-Agent' => 'xiaomiquan/3.18.0 iOS/phone/12.1.1',
                    'Host'     => 'api.zsxq.com',
                    'Authorization'  => 'CB1D1B6F-479A-844D-4C82-8FBF641636E3',
                    'X-Request-Id'  => '21fa0fbd-fbfb-4eea-a1e2-ed0d6850bfb5',
                    'X-Version'  => '1.10.10',
                ]
            ];
        }
        $response = $this->client->request('GET', $uri, $header);
        return json_decode($response->getBody(), true);
    }

}