<?php
namespace App\Service;

use Wechat\Wechat;
use App\Service\RedisService;

class WechatTplService
{
    private $wechat;

    public function __construct(RedisService $redis)
    {
        $appid = $this->request->server->get('WX_APPID');
        $secret = $this->request->server->get('WX_SECRET');
        $access_token = $redis->getOrNew('wechat_access_token', function() use($appid, $secret) {
            $wechat = new Wechat($appid, $secret);
            return $wechat->token();
        }, 3600);
        $this->wechat = new Wechat($appid, $secret, false);
    }

    public function getWechat()
    {
        return $this->wechat;
    }

    public function sendBind($openid, $account)
    {
        
    }

    public function sendUnbind($openid, $account)
    {

    }

    public function sendScore($openid, $name, $score, $type)
    {

    }
}