<?php

namespace Quiz\Controller;

use Zend\Mvc\Controller\ActionController;
use Zend\Mvc\LocatorAware;

require 'facebook/facebook.php';

class IndexController extends ActionController implements LocatorAware
{

    public function indexAction()
    {
//        $facebook = new \Facebook(array(
//          'appId'  => '322152434467439',
//          'secret' => 'bd125fa90026c5faba6ed397026c53f0',
//        ));
//
//        $user = $facebook->getUser();
//        if ($user) {
//            try {
//                // Proceed knowing you have a logged in user who's authenticated.
//                $user_profile = $facebook->api('/me');
//                var_dump($user_profile);
//            } catch (FacebookApiException $e) {
//                error_log($e);
//                $user = null;
//            }
//        }
//
//        if ($user) {
//            $logoutUrl = $facebook->getLogoutUrl();
//        } else {
//            $loginUrl = $facebook->getLoginUrl();
////            $this->plugin('redirect')->toUrl($loginUrl);
//        }

//        return array('loginUrl' => $loginUrl);
    }

    protected function attachDefaultListeners()
    {
        parent::attachDefaultListeners();

        /** @var $events \Zend\EventManager\EventManager */
        $events = $this->events();

        $events->attach('dispatch', function ($e) {
            $e->setParam('layout', 'layouts/app.phtml');
        }, 100);
    }
}
