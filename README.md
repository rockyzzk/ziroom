# ZIROOM
自如租房监控，发送邮件提醒
### 说明
在学习Laravel框架，有租房需求，所以大材小用一波

### 环境需要
* PHP
* Redis

### 部署
- laravel 环境搭建
    *在根目录依次输入*
    - cp .env.example .env(linux)
    - php artisan key:generate
    - composer install
    > 参考：[本地部署基于laravel的项目踩坑总结](https://segmentfault.com/a/1190000010040259)

- 项目配置
    *修改根目录 .env 文件*

    * 邮件配置
        - MAIL_TO_ADDRESS=你的邮箱地址
         > 参考：[smtp发送邮件，参数应该怎么配置？](http://wenda.golaravel.com/question/152)

    * 房源配置
        - ZIROOM_CODE=自如房间ID
         > 说明： 获取方式：web端访问自如房源页面，取页面网址最后的数字例如：若想监控http://www.ziroom.com/z/vr/61291696.html 此房源，则在这里配置61291696；
若配置多个，以“,”分隔。
