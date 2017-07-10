项目说明
#### 项目 clone

```
git clone https://github.com/stoneworld/NeteaseCloudMusic
cd viease
composer install -v
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

#### 创建.env
`cp .env.example .env && composer install && php artisan key:generate`

#### 创建数据库
`php artisan migrate`

#### 队列处理
配置队列，如果不想安装 `Supervisor`，执行 `php artisan queue:work`

#### 执行命令
爬取某人的歌单以及评论 `php artisan netease:playlist 123456` 将123456替换成网易云音乐用户的 id，等待结束后，表 `user_comments` 就能看到用户的评论了
