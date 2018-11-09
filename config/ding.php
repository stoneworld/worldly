<?php
/**
 * Created by PhpStorm.
 * User: wangshuai
 * Date: 2018/11/9
 * Time: 11:33 AM
 */
return [

    'default' => [
        'enabled' => env('DING_ENABLED',true),

        'token' => env('DING_TOKEN',''),

        'timeout' => env('DING_TIME_OUT',2.0)
    ],

];