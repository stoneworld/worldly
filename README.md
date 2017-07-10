项目说明
1. `cp .env.example .env && composer install && php artisan key:generate`
2. `php artisan migrate`
3. 配置队列，如果不想安装 `Supervisor`，执行 `php artisan queue:work`
4. 爬取某人的歌单以及评论 `php artisan netease:playlist 123456` 将123456替换成网易用户的 id
5. 表 `user_comments` 就能看到用户的评论了
