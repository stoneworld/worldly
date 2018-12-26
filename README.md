项目说明
#### 项目 clone
> 接口有没有变更 不太清楚 有问题自行处理
```
git clone https://github.com/stoneworld/NeteaseCloudMusic.git
cd NeteaseCloudMusic
composer install -v
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

#### 创建.env
`cp .env.example .env && php artisan key:generate`

#### 创建数据库
`php artisan migrate`

#### 队列处理
配置队列，如果不想安装 `Supervisor`，执行 `php artisan queue:work`

#### 执行命令
先执行代理获取的命令 `php artisan getFreeProxy`，100个免费代理，但效果貌似不太好，毕竟免费。
爬取某人的歌单以及评论 `php artisan netease:playlist 123456` 将123456替换成网易云音乐用户的 id，等待结束后，表 `user_comments` 就能看到用户的评论了

> 貌似爬的太多会503，后面找点可用代理去处理。
