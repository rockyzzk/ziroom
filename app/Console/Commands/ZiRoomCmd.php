<?php

namespace App\Console\Commands;

use App\Mail\ZiRoomMailer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redis;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class ZiRoomCmd extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ziroom';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'ZiRoom';

    protected $recvMail = '809721719@qq.com';

    protected $uri = [];

    protected $codes = [];

    protected $redisSecond = 0;

    protected $sendSecond = 0;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->uri = env('ZIROOM_URI');
        $this->redisSecond = env('ZIROOM_MAIL_SECOND', 3600);
        $this->sendSecond = env('ZIROOM_CHECK_SECOND', 10);
        $this->codes = explode(',', env('ZIROOM_CODE'));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $client = new Client();

        while (true) {
            foreach ($this->codes as $code) {

                $options['headers'] = array(
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/57.0.2987.133 Safari/537.36',
                );
                $url = $this->uri . $code;

                try {
                    // 请求房源
                    $res = $client->request('POST', $url, $options);

                    // 分析状态
                    if ($res->getStatusCode() == 200) {
                        $responseBody = $res->getBody();

                        if (strstr($responseBody, 'title="配置中"')) {
                            echo getTime() . ' 房源：' . $code . ' 配置中' . PHP_EOL;
                        } elseif (strstr($responseBody, '请核对您输入的页面地址是否正确哦')) {
                            echo getTime() . ' 房源：' . $code . ' 不存在' . PHP_EOL;
                        } elseif (strstr($responseBody, '已出租')) {
                            echo getTime() . ' 房源：' . $code . ' 已出租' . PHP_EOL;
                        } else {
                            echo getTime() . ' 房源：' . $url . ' 可预定！！！' . PHP_EOL;

                            if (!Redis::exists(getRedisKey($code))) {
                                // 发送邮件
                                Mail::to($this->recvMail)->send(new ZiRoomMailer($url, getTitle($responseBody)));
                                // 一小时提醒一次
                                Redis::set(getRedisKey($code), 1);
                                Redis::expire(getRedisKey($code), $this->redisSecond);
                            }
                        }
                    }
                } catch (ClientException $e) {
                    echo getTime() . ' 路径：' . $url . ' 不存在' . PHP_EOL;
                }
            }
            sleep($this->sendSecond);
        }
    }
}

function getTime()
{
    return date('Y-m-d H:i:s', time());
}

function getTitle($subject)
{
    $pattern = '/<title>([\S\s]*?)<\/title>/';
    preg_match($pattern, $subject, $matchs);

    return $matchs[1];
}

function getRedisKey($code)
{
    $roomKey = 'room:' . $code;

    return $roomKey;
}