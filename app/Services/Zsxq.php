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
     * @author wangshuai15@100tal.com
     * @param $groupId
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPublicTopicsByGroupId($groupId)
    {
        $uri = '/v1.10/groups/'.$groupId.'/public_topics';
        return $this->requestGet($uri);
    }


    /**
     * 获取圈子的基本信息
     * @author wangshuai15@100tal.com
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
     * @author wangshuai15@100tal.com
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
                    'User-Agent' => 'xiaomiquan/3.15.1 iOS/phone/12.1.1',
                    'Host'     => 'api.zsxq.com',
                    'Authorization'  => '9338191B-A195-21E8-60DA-9FF5706A6CB4',
                    'X-Request-Id'  => '3a800375-45bb-4573-8fe9-dec67fea20d6',
                    'X-Version'  => '1.10.8',
                ]
            ];
        }
        $response = $this->client->request('GET', $uri, $header);
        return json_decode($response->getBody(), true);
    }

}