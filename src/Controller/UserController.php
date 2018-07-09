<?php

namespace App\Controller;

use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Nefu\Nefuer;
use Wechat\Wechat;

class UserController extends Controller
{
    /**
     * 2、判断是否微信打开
     */
    public function index(): JsonResponse
    {
        if ($this->session->has('openid')) {
            return $this->signByOpenid($this->request, $this->session);
        } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
            return $this->wxIn();
        } else {
            return $this->toSign();
        }
    }

    /**
     * 检查是否登录
     */
    public function checkSign(): JsonResponse
    {
        $nefuer = $this->getNefuer();
        if (false == $nefuer) {
            return $this->error();
        } else {
            return $this->success();
        }
    }

    /**
     * 3、微信登录
     */
    public function wxIn(): JsonResponse
    {
        $appid = $this->request->server->get('WX_APPID');
        $secret = $this->request->server->get('WX_SECRET');
        $wechat = new Wechat($appid, $secret, false);
        if ( ! $this->request->request->has('code')) {
            //4、获取openid
            $redirectUri = $this->generateUrl(
                'wxIn',
                array(),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $url = $wechat->code($redirectUri, false);
            return $this->toUrl($url, '自动登录中');
        } else {
            //5、保存openid至session
            $code = $this->request->request->has('code');
            $info = $wechat->base($code);
            $openid = $info['openid'];
            $this->session->set('nefuer_openid', $openid);
            return $this->signByOpenid();
        }
    }

    /**
     * 通过openid登录
     */
    private function signByOpenid(): JsonResponse
    {
        $openid = $this->session->get('openid');
        //6、根据openid获取用户信息

        //8、检查是否绑定
        if(true/* condition */) {
            //9、绑定：登录
            $rst = $this->sign($account, $password);
            if (false === $rst) {

            } else {
                
            }
        } else {
            //14、未绑定，返回登录
            return $this->toSign();
        }
        
    }

    /**
     * 登录
     */
    public function sign($account = null, $password = null)
    {
        $local = ( ! is_null($account));
        $account  = $this->request->request->get('account');
        $password = $this->request->request->get('password');
        if (empty($account)) {
            return $this->error(self::PARAM_MISS, '账号不能为空');
        }
        if (empty($password)) {
            return $this->error(self::PARAM_MISS, '密码不能为空');
        }

        $nefuer = new Nefuer();
        $login = $nefuer->login($account, $password);
        
        switch($login['code']) {
            case 0:
                $this->session->set('nefuer_account', $account);
                $this->session->set('nefuer_password', $password);
                $this->session->set('nefuer_cookie', $nefuer->getCookie());
                return $this->success();
            case 100:
                if ($local) {
                    return false;
                } else {
                    return $this->success();
                }
            case 201:
                return $this->error(null, '账号或密码错误');
        }
        return $this->success($login);
    }
}
