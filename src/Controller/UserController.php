<?php

namespace App\Controller;

use App\Controller\BaseController as Controller;
use Symfony\Component\HttpFoundation\Response;
use Nefu\Nefuer;
use Wechat\Wechat;

class UserController extends Controller
{
    public function index(): Response
    {
        $flashBag = $this->session->getFlashBag();
        $ope     = $flashBag->get('ope', array(null))[0];
        $opeType = $flashBag->get('opeType', array(null))[0];
        
        $ope     = $ope     ?? $this->request->query->get('ope', '/');
        $opeType = $opeType ?? $this->request->query->get('opeType', 'abstractUrl');
        
        if ($this->session->has('student')) {
            return $this->redirect($this->buildRedirectUrl($ope, $opeType));
        } else {
            $flashBag->add('ope', $ope);
            $flashBag ->add('opeType', $opeType);
            if ($this->session->has('openid')) {
                return $this->signByWx($this->request, $this->session);
            } elseif (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger') !== false) {
                return $this->wxIn();
            } else {
                return $this->toSign();
            }
        }
    }

    public function login(): Response
    {
        return $this->render('user/login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function wxIn(): Response
    {
        $appid = $this->request->server->get('WX_APPID');
        $secret = $this->request->server->get('WX_SECRET');
        $wechat = new Wechat($appid, $secret, false);
        if ( ! $this->request->request->has('code')) {
            $redirectUri = $this->generateUrl(
                'wxIn',
                array(),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $url = $wechat->code($redirectUri, false);
            return $this->toUrl(array(
                'url' => $url
            ), '自动登录中');
        } else {
            $code = $this->request->request->has('code');
            $info = $wechat->base($code);
            $openid = $info['openid'];

        }
    }

    /**
     * 登录
     */
    public function sign() {
        $flashBag = $this->session->getFlashBag();
        if (is_null($account) && ! $this->request->request->has('account')) {
            $ope     = $flashBag->get('ope', array(null))[0];
            $opeType = $flashBag->get('opeType', array(null))[0];
            return $this->render('User/login.html.twig', array(
                'loginUrl' => $this->generateUrl('sign'),
                'transferPage' => $this->buildRedirectUrl($ope, $opeType),
            ));
        } else {
            $account  = $account  ?? $this->request->request->get('account');
            $password = $password ?? $this->request->request->get('password');

            if (empty($account)) {
                return $this->json(array(
                    'code' => 1,
                    'msg' => '账号不能为空',
                ));
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

    /**
     * 通过openid登录
     */
    private function signByWx()
    {
        $openid = $this->session->get('openid');
    }

    private function buildRedirectUrl($ope, $opeType = 'absoluteUrl'): string
    {
        if ($opeType === 'absoluteUrl' && strpos($ope, '://') !== false) {
            $opeType = 'route';
        }
        switch ($opeType) {
            case 'route':
                return $this->generateUrl($ope);
            case 'uri':
                //no break;
            default:
                return $ope;
            case 'absoluteUrl':
                return sprintf('%s?acc=', $ope, $acc, $name);
        }
    }
}
