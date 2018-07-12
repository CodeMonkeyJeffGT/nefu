<?php
namespace App\Service;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\HttpFoundation\Request;
use Wechat\Wechat;
use App\Service\RedisService;

class WechatService
{
    private $wechat;

    public function __construct(RedisService $redis)
    {
        $request = Request::createFromGlobals();
        $appid = $request->server->get('WX_APPID');
        $secret = $request->server->get('WX_SECRET');
        $access_token = $redis->getOrNew('wechat_access_token', function() use($appid, $secret) {
            $wechat = new Wechat($appid, $secret);
            return $wechat->token();
        }, 3600);
        $this->wechat = new Wechat($appid, $secret, $access_token);
    }

    public function getWechat()
    {
        return $this->wechat;
    }

    public function sendBind($openid, $account)
    {
        $url = $_SERVER['SERVER_NAME'];
        $template_id = 'Opw5l9pNNPJKu_Pm7tJVvdFcT02AKE1jKRqOyf5BQnk';
        $data = array(
            "first" => array(
                "value" => "您已为该微信绑定学号",
                "color" => "#173177"
            ),
            "keyword1" => array(
                "value" => $account,
                "color" => "#173177"
            ),
            "keyword2" => array(
                "value" => date('Y-m-d H:i:s'),
                "color" => "#173177"
            ),
            "remark" => array(
                "value" => "此微信账号查成绩无需手动填写密码\n已自动为您开通成绩推送功能",
                "color" => "#173177"
            ),
        );
        return $this->wechat->tplMsg($openid, $template_id, $data, $url);
    }

    public function sendUnbind($openid, $account)
    {
        $url = $_SERVER['SERVER_NAME'];
        $template_id = 'ggoURgkU3MwSZf7IC6WXU7dC3BZEcNMRzboymrAOK2Y';
        $data = array(
            "first" => array(
                "value" => "您已解除绑定",
                "color" => "#173177"
            ),
            "keyword1" => array(
                "value" => $account,
                "color" => "#173177"
            ),
            "keyword2" => array(
                "value" => date('Y-m-d H:i:s'),
                "color" => "#173177"
            ),
            "remark" => array(
                "value" => "如不是您本人操作，请尽快修改教务系统密码",
                "color" => "#173177"
            ),
        );
        return $this->wechat->tplMsg($openid, $template_id, $data, $url);
    }

    public function sendScore($openid, $name, $score, $type, $num, $update = false)
    {
        $url = 'http://nefuer.net';
        $template_id = 'Zu1NRhwKYW1jsdmD9dE8ukK3g4iCwKzKFy_wlfROMZE';
        $data = array(
            'first' => array(
				'value' => $update ? '您有一个成绩有变化' : '新成绩！',
				'color' => '#173177'
			),
			'keyword1' => array(
				'value' => $name,
				'color' => '#173177'
			),
			'keyword2' => array(
				'value' => $type,
				'color' => '#173177'
			),
			'keyword3' => array(
				'value' => $score,
				'color' => '#173177'
			),
			'keyword4' => array(
				'value' => $num,
				'color' => '#173177'
			),
			'remark' => array(
				'value' => '点击查看全部成绩',
				'color' => '#173177'
			)
        );
        return $this->wechat->tplMsg($openid, $template_id, $data, $url);
    }
}