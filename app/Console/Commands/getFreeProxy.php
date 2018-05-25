<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;

class getFreeProxy extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'getFreeProxy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '定时获取免费代理';

    /**
     * Create a new command instance.
     *
     * @return void
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
        $this->_getIp181FreeProxy();
    }

    private function _getIp181FreeProxy()
    {
        $url = 'http://www.ip181.com/';
        $content = file_get_contents($url);
        $content = iconv('gbk', 'utf-8', $content);
        $pattern = "/<tr.*>(.*)<\/tr>/iUs";
        preg_match_all($pattern, $content, $matches);
        $pattern1 = "/<td>(.*)<\/td>/";
        $ipList = [];
        foreach ($matches[1] as $key => $value) {
	        if ($key == 0) continue;
	        preg_match_all($pattern1, $value, $matches1);
	        $ipList[] = [
		        'ip' => $matches1[1][0],
		        'port' => $matches1[1][1],
	        ];
        }
        DB::table('proxy_ips')->truncate();
        DB::table('proxy_ips')->insert($ipList);
    }
}
