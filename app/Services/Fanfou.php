<?php
/**
 * User: wangshuai
 */

namespace App\Services;


class Fanfou
{

    public function register()
    {
        $response = $this->cget('http://fanfou.com/register');
        return $response;
    }
    public function cget($url, $timeout = 5)
    {
        $curl = curl_init();

        if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')){
            curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        }
        curl_setopt ( $curl, CURLOPT_CONNECTTIMEOUT,$timeout);
        curl_setopt ( $curl, CURLOPT_TIMEOUT, $timeout);

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);


        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

}