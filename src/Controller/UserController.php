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
    public function index(): Response
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
     * 3、微信登录
     */
    public function wxIn(): Response
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
            echo $redirectUri;die;
            $url = $wechat->code($redirectUri, false);
            return $this->toUrl(array(
                'url' => $url
            ), '自动登录中');
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
    private function signByOpenid()
    {
        $openid = $this->session->get('openid');
        //6、根据openid获取用户信息

        //8、检查是否绑定
        if(true/* condition */) {
            //9、绑定：登录
            $rst = $this->sign($account, $password);
            var_dump($rst);die;
        } else {
            //14、未绑定，返回失败
        }
        
    }

    /**
     * 登录
     */
    public function sign($account = null, $password = null)
    {
        if (is_null($password)) {
            if (empty($account)) {
                return $this->json(array(
                    'code' => 1,
                    'msg' => '账号不能为空',
                ));
            }
            if (empty($password)) {
                return $this->json(array(
                    'code' => 1,
                    'msg' => '密码不能为空',
                ));
            }
            $account  = $this->request->request->get('account');
            $password = $this->request->request->get('password');
            $passsword = strtoupper(md5($password));
        }

        $nefuer = new Nefuer();
        $login = $nefuer->login($account, $password);
        $loginStatus = false;
        if ($login['code'] == 0) {
            $loginStatus = true;
            // $this->session->set('student', array(
            //     ''
            // ))
        }

        if ($loginStatus && $this->request->request->has('account')) {
            //$this->session todo
        } elseif($loginStatus) {

        } elseif ($this->request->request->has('account')) {
            //return json
        } else {
            //return false
        }
    }
}
