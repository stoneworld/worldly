<?php

namespace App\Services;

class Netease
{
    const MODULUS = '00e0b509f6259df8642dbc35662901477df22677ec152b5ff68ace615bb7b725152b3ab17a876aea8a5aa76d2e417629ec4ee341f56135fccf695280104e0312ecbda92557c93870114af6c9d05c4f7f0c3685b7a46bee255932575cce10b424d813cfe4875d3e82047b97ddef52741d546b8e289dc6935b3ece0462db0a22b8e7';

    const NONCE = '0CoJUm6Qyw8W8jud';

    const PUBKEY = '010001';

    protected $headers = ['Accept: */*', 'Accept-Encoding: gzip,deflate,sdch', 'Accept-Language: zh-CN,zh;q=0.8,gl;q=0.6,zh-TW;q=0.4', 'Connection: keep-alive', 'Content-Type: application/x-www-form-urlencoded', 'Host: music.163.com', 'Referer: http://music.163.com/search/', 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_2) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/33.0.1750.152 Safari/537.36'];
    protected $secretKey;
    protected $curl;

    public function __construct()
    {
        $this->secretKey = $this->createSecretKey(16);
        $this->curl = new ProxyCurl();
    }

    protected function createSecretKey($length)
    {
        $str = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $r = '';
        for ($i = 0; $i < $length; $i++) {
            $r .= $str[rand(0, strlen($str) - 1)];
        }
        return $r;
    }

    protected function prepare($data)
    {
        $data['params'] = $this->aesEncrypt($data['params'], self::NONCE);
        $data['params'] = $this->aesEncrypt($data['params'], $this->secretKey);
        $data['encSecKey'] = $this->rsaEncrypt($this->secretKey);
        return $data;
    }

    protected function aesEncrypt($secretData, $secret)
    {
        return openssl_encrypt($secretData, 'aes-128-cbc', $secret, false, '0102030405060708');
    }

    /**
     * @param $text
     * @return string
     */
    protected function rsaEncrypt($text)
    {
        $rtext = strrev(utf8_encode($text));
        $keytext = $this->bchexdec($this->strToHex($rtext));
        $biText = new Math_BigInteger($keytext);
        $biKey = new Math_BigInteger($this->bchexdec(self::PUBKEY));
        $biMod = new Math_BigInteger($this->bchexdec(self::MODULUS));
        $key = $biText->modPow($biKey, $biMod)->toHex();
        return str_pad($key, 256, '0', STR_PAD_LEFT);
    }

    protected function bchexdec($hex)
    {
        $dec = 0;
        $len = strlen($hex);
        for ($i = 0; $i < $len; $i++) {
            $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i])), bcpow('16', strval($len - $i - 1))));
        }
        return $dec;
    }

    protected function strToHex($str)
    {
        $hex = '';
        for ($i = 0; $i < strlen($str); $i++) {
            $hex .= dechex(ord($str[$i]));
        }
        return $hex;
    }

    protected function curl($url, $data = null)
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
        curl_setopt($curl, CURLOPT_REFERER, 'http://music.163.com/');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($curl, CURLOPT_ENCODING, 'application/json');
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 搜索API
     * @param $s 要搜索的内容
     * @param $limit 要返回的条数
     * @param $offset 设置偏移量 用于分页
     * @param $type 类型 [1 单曲] [10 专辑] [100 歌手] [1000 歌单] [1002 用户]
     * @return JSON
     */
    public function search($s = null, $limit = 30, $offset = 0, $type = 1)
    {
        $url = 'http://music.163.com/weapi/cloudsearch/get/web?csrf_token=';
        $data = ['params' => '{
              "s":"' . $s . '",
              "type":"' . $type . '",
              "limit":"' . $limit . '",
              "total":"true",
              "offset":"' . $offset . '",
              "csrf_token": ""
          }'];
        return $this->curl->retryCurl($url, $this->prepare($data));
    }

    /**
     * 歌曲详情API，不带MP3链接
     * @param $song_id 歌曲id
     * @return JSON
     */
    public function detail($song_id)
    {
        $url = 'http://music.163.com/weapi/v1/song/detail';
        if (is_array($song_id)) $s = '["' . implode('","', $song_id) . '"]'; else $s = '["' . $song_id . '"]';
        $data = ['params' => '{
                "ids":' . $s . ',
                "csrf_token":""
            }'];
        return $this->curl->retryCurl($url, $this->prepare($data));
    }


    /**
     * 歌单API
     * @param $playListId
     * @return JSON
     */
    public function playListInfo($playListId)
    {
        $url = 'http://music.163.com/weapi/v3/playlist/detail?csrf_token=';
        $data = ['params' => '{
                "id":"' . $playListId . '",
                "n":"1000",
                "csrf_token":""
            }'];
        $result = $this->curl->retryCurl($url, $this->prepare($data));
        return json_decode($result, true);
    }

    /**
     * userPlaylists description
     * @param  [type] $userId [description]
     * @return [JSON]         [description]
     */
    public function userPlaylists($userId)
    {
    	$url = 'http://music.163.com/weapi/user/playlist?csrf_token=';
        $data = ['params' => '{
                "uid":"' . $userId . '",
                "offset":"0",
                "limit":"100",
                "csrf_token":""
            }'];
        $response = $this->curl->retryCurl($url, $this->prepare($data));
        return json_decode($response, true)['playlist'];
    }

    public function songComment($musicId,$offset)
    {
    	$url = 'http://music.163.com/weapi/v1/resource/comments/R_SO_4_'. $musicId .'?csrf_token=';
    	$data = ['params' => '{
                "offset":"'.$offset.'",
                "limit":"100",
                "total": true,
                "csrf_token":""
            }'];
        $response = $this->curl->retryCurl($url, $this->prepare($data));
        $comments = json_decode($response, true);
        return $comments;
    }

    public function songHistory()
    {

    }
}