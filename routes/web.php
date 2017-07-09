<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::post('article/{id}/vote', 'ArticleController@vote');
Route::get('market/list', 'MarketController@listItem');












Route::get('redis/get', function () {
    //echo phpinfo();
    Redis::zAdd('k1', 0, 'val0');
    Redis::zAdd('k1', 1, 'val1');
    Redis::zAdd('k1', 3, 'val3');
    Redis::zAdd('k2', 2, 'val1');
    Redis::zAdd('k2', 3, 'val3');
    Redis::zinterstore('ko3', array('k1', 'k2'), array(0, 5));
    return Redis::zrange('ko3', 0, -1);

    $time = time();
    Redis::hmset('test2', ['te' => 12, 'q2' => '12']);
    dump(Redis::hmget('test2', ['te', 'q2']));
    Redis::set('name', 'Taylor');
    Redis::set('name1', 'Taylsor');
    return Redis::get('name1');
});

Route::get('redis/hash', function () {
    Redis::hset('hash', '1', 'test');
    Redis::hset('hash', '2', 'test2');
    return Redis::hgetall('hash');
});

Route::get('redis/zset', function () {
    Redis::zadd('zset', '100', 'member1');
    Redis::zadd('zset', '200', 'member2');
    dump(Redis::zrange('zset', 0, -1, 'withscores'));
    dump(Redis::zrangebyscore('zset', 0, 150, 'withscores'));
});


