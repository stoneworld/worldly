<?php

namespace App\Services;

use DB;
use Log;

class ProxyCurl
{
    protected $headers = ['Accept: */*', 'Accept-Encoding: gzip,deflate,sdch', 'Accept-Language: zh-CN,zh;q=0.8,gl;q=0.6,zh-TW;q=0.4', 'Connection: keep-alive', 'Content-Type: application/x-www-form-urlencoded', 'Host: music.163.com', 'Referer: http://music.163.com/search/', 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.36'];

    public function curl($url, $data = null, $referer = 'http://music.163.com/', $ip)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        if ($data) {
            if (is_array($data)) $data = http_build_query($data);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_POST, 1);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_REFERER, $referer);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_ENCODING, 'application/json');
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        if (!empty($ip)) {
            curl_setopt($curl, CURLOPT_PROXY, $ip);
        }
        $result = curl_exec($curl);
        if (curl_errno($curl)) {
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            Log::info(curl_error($curl));
            echo "curl Request Error: $url " . curl_error($curl) . " $http_status \n";
            return '';
        }
        if (empty($result)) {
            return '';
        }
        curl_close($curl);
        return $result;
    }

    public function retryCurl($url, $data = null, $referer = 'http://music.163.com/', $retry = 15)
    {
        $try = 0;
        $ip = '';
        $result = $this->curl($url, $data, $referer, $ip);
        while (empty($result)) {
            if ($try <= $retry) {
                $ip = $this->getProxyIp();
                $result = $this->curl($url, $data, $referer, $ip);
                $try++;
                echo "retry url: $try $url \n";
            } else {
                echo "fail retry url: $try $url \n";
                break;
            }
        }
        return $result;
    }

    public function getProxyIp()
    {
        $ips = DB::table('proxy_ips')->get()->toArray();
        if (!empty($ips)) {
            $key = array_rand($ips, 1);
            $ip = $ips[$key]->ip . ':' . $ips[$key]->port;
            return $ip;
        }
        return '';
    }
} 
