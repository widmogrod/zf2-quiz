<?php

namespace Quiz\Controller;

use Zend\Mvc\Controller\ActionController,
    Zend\Http\PhpEnvironment\Response,
    Zend\Json\Json;

class IndexController extends ActionController
{

    public function indexAction()
    {
//        /** @var $facebook \Facebook */
//        $facebook = $this->getLocator()->get('facebook');
//        $user = $facebook->getUser();
//        if ($user) {
//            var_dump($user);
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
//            $this->plugin('redirect')->toUrl($loginUrl);
//        }

        return array();
    }

    public function startAction()
    {
        /** @var $model \Quiz\Model\Front */
        $model = $this->getLocator()->get('quiz-model');

        if (!$model->isAuth()) {
            $this->plugin('redirect')->toUrl($model->getLoginUrl());
        }

//        return array('canPlay' => $model->canPlayAgain());
    }

    public function getquizAction()
    {
        /** @var $model \Quiz\Model\Front */
        $model = $this->getLocator()->get('quiz-model');

        if (!$model->isAuth()) {
            $this->plugin('redirect')->toUrl($model->getLoginUrl());
        }

        $response = new Response();
        $response->setContent(Json::encode($model->getRandomQuestions()));

        return $response;
    }

    public function resultsAction()
    {
        /** @var $model \Quiz\Model\Front */
        $model = $this->getLocator()->get('quiz-model');

        if (!$model->isAuth()) {
            $this->plugin('redirect')->toUrl($model->getLoginUrl());
        }

        /** @var $rq \Zend\Http\PhpEnvironment\Request */
        $rq = $this->getRequest();

        $result = true;
        if ($rq->isPost())
        {
            $data = $this->getRequest()->post()->toArray();
            $quizId  = (int) $data['quizId'];
            $answers = (array) $data['answers'];

            $result = $model->saveAnswersForQuiz($quizId, $answers);
        }

        if (true === $result) {
            $result = $model->getResultsForThisWeek();
        }

        $response = new Response();
        $response->setContent(Json::encode($result));

        return $response;
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
