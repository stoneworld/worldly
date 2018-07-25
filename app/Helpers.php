<?php
function getSinaShortUrl($url_long, $showHttp = false)
{
    $source = 2303085031;

    // 参数检查
    if (empty($source) || !$url_long) {
        return false;
    }

    // 参数处理，字符串转为数组
    if (!is_array($url_long)) {
        $url_long = array($url_long);
    }

    // 拼接url_long参数请求格式
    $url_param = array_map(function ($value) {
        return '&url_long=' . urlencode($value);
    }, $url_long);

    $url_param = implode('', $url_param);

    // 新浪生成短链接接口
    $api = 'http://api.t.sina.com.cn/short_url/shorten.json';

    // 请求url
    $request_url = sprintf($api . '?source=%s%s', $source, $url_param);

    // 执行请求
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_URL, $request_url);
    $data = curl_exec($ch);
    if ($error = curl_errno($ch)) {
        return false;
    }
    curl_close($ch);
    
    $result = json_decode($data, true);
    if (!$showHttp) {
        foreach ($result as &$value) {
            $value['url_short'] = str_replace('http://', '', $value['url_short']);
        }
    }
    return $result;
}

function encode($string, $key) {
    $j = 0;
    $hash = '';
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string, $i, 1));
        if ($j == $keyLen) {
            $j = 0;
        }
        $ordKey = ord(substr($key, $j, 1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey), 16, 36));
    }
    return $hash;
}