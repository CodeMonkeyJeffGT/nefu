<?php

namespace App\Controller;

use Wechat\Wechat;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController extends Controller
{
    private $flashBag;

    public function __construct(SessionInterface $session)
    {
        $this->flashBag = $session->getFlashBag();
    }

    public function index(Request $request, SessionInterface $session)
    {
        $this->flashBag = $session->getFlashBag();

        $ope     = $this->flashBag->get('ope', array(null))[0];
        $opeType = $this->flashBag->get('opeType', array(null))[0];
        
        $ope     = $ope     ?? $request->query->get('ope', '/');
        $opeType = $opeType ?? $request->query->get('opeType', 'abstractUrl');
        
        if ($session->has('student')) {
            return $this->buildRedirect($ope, $opeType);
        } else {
            $this->flashBag->add('ope', $ope);
            $this->flashBag ->add('opeType', $opeType);
            if ($session->has('openid')) {
                return $this->signByWx($request, $session);
            } else {
                return $this->wxIn($request, $session);
            }
        }
    }

    public function login(Request $request, SessionInterface $session)
    {
        return $this->render('user/login.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    public function wxIn(Request $request, SessionInterface $session)
    {
        $appid = $request->server->get('WX_APPID');
        $secret = $request->server->get('WX_SECRET');
        echo '<pre>';
        var_dump(array(
            $appid, $secret
        ));
        $wechat = new Wechat($appid, $secret, false);
        if ( ! $request->request->has('code')) {
            $redirectUri = $this->generateUrl(
                'wxIn',
                array(),
                UrlGeneratorInterface::ABSOLUTE_URL
            );
            $url = $wechat->code($redirectUri, false);
            return $this->redirect($url);
        } else {
            $code = $request->request->has('code');
            $info = $wechat->base($code);
            $openid = $info['openid'];

        }
    }

    /**
     * 登录
     */
    public function sign(
        Request $request,
        SessionInterface $session,
        $account = null,
        $password = null
    ) {
        if (is_null($account) && ! $request->request->has('account')) {
            $ope     = $this->flashBag->get('ope', array(null))[0];
            $opeType = $this->flashBag->get('opeType', array(null))[0];
            return $this->render('User/login.html.twig', array(
                'loginUrl' => $this->generateUrl('login'),
                'transferPage' => $this->buildRedirect($ope, $opeType),
            ));
        } else {
            $account  = $account  ?? $request->request->get('account');
            $password = $password ?? $request->request->get('password');
            //todo
            if (true) {
                //$session todo
            } elseif (($request->request->has('account'))) {
                //return json
            } else {
                //return false
            }
        }
    }

    /**
     * 通过openid登录
     */
    private function signByWx(Request $request, SessionInterface $session)
    {
        $openid = $session->get('openid');
    }

    private function buildRedirect($ope, $opeType = 'absoluteUrl')
    {
        if ($opeType === 'absoluteUrl' && strpos($ope, '://') !== false) {
            $opeType = 'route';
        }
        switch ($opeType) {
            case 'route':
                return $this->redirectToRoute($ope);
            case 'uri':
                //no break;
            default:
                return $this->redirect($ope);
            case 'absoluteUrl':
                return $this->redirect(sprintf('%s?acc=', $ope, $acc, $name));
        }
    }
}
