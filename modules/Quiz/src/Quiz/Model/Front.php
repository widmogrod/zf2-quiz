<?php
namespace Quiz\Model;

use Doctrine\ORM\EntityManager;

/**
 * @author Gabriel Habryn <gabriel.habryn@me.com>
 */
class Front
{
    /**
     * @var \Facebook
     */
    protected $facebook;

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    protected $isAuth;

    public function __construct(\Facebook $facebook, EntityManager $entityManager)
    {
        $this->facebook = $facebook;
        $this->entityManager = $entityManager;
    }

    public function getUserEntity()
    {
        if (!$this->isAuth()) {
            return false;
        }

        try {
            /** @var $user \Quiz\Repository\User */
            $user = $this->entityManager->getRepository('Quiz\Entity\User');
            return $user->createFacebookUser($this->getFacebookUserId(), $this->getFacebookData());
        } catch (\Exception $e) {
//            \Zend\Debug::dump($e->getMessage());
//            \Zend\Debug::dump($e->getFile());
//            \Zend\Debug::dump($e->getLine());
//            \Zend\Debug::dump($e->getTrace());
        }

        return false;
    }

    public function isAuth()
    {
        if (isset($_GET['debug']))
        {
            var_dump($this->facebook->getSignedRequest());
            var_dump($this->facebook->getAccessToken());
            var_dump($this->facebook->getUser());
        }

        return $this->facebook->getUser() > 0;
    }

    public function getFacebookData()
    {
        $result = array();
        try {
            $result = $this->facebook->api('/me');
        } catch(\Exception $e) {
//            throw $e;
//            \Zend\Debug::dump($e->getMessage());
//            \Zend\Debug::dump($e->getFile());
//            \Zend\Debug::dump($e->getLine());
//            \Zend\Debug::dump($e->getTrace());
        }

        return $result;
    }

    public function getFacebookUserId()
    {
        return $this->facebook->getUser();
    }

    public function getLoginUrl()
    {
        return $this->facebook->getLoginUrl();
    }

    public function getUserId()
    {
        if (($user = $this->getUserEntity())) {
            return $user->getId();
        }

        return $user;
    }

    public function getRandomQuestions()
    {
        $result = array(
            'status' => false,
            'message' => 'Dziękujemy za rozgrywkę, tylko dwa razy dziennie można wziąć udział w quiz-ie. Zapraszamy ponownie jutro!',
            'result' => array()
        );

        if ($this->canPlayAgain())
        {
            /** @var $quiz \Quiz\Repository\Quiz */
            $quiz = $this->entityManager->getRepository('Quiz\Entity\Quiz');
            $questions = $quiz->getQuestions($this->getUserEntity());

            $result['status'] = true;
            $result['result'] = $questions;
        }

        return $result;
    }

    public function getResultsForThisWeek()
    {
        $result = array(
            'status' => true,
            'message' => null,
            'result' => array()
        );

        /** @var $quiz \Quiz\Repository\Quiz */
        $quiz = $this->entityManager->getRepository('Quiz\Entity\Quiz');
        $status = $quiz->getResultsForThisWeek();
        if (!is_array($status)) {
            $result['status'] = false;
            $result['message'] = 'Niezmiernie nam przykro, ale podczas pobierania wyników serwer odmówił posłuszeństwa! Proszę odświerz stronę!';
        } else {
            $result['result'] = $status;
        }

        return $result;
    }

    public function saveAnswersForQuiz($quizId, array $data)
    {
        $result = array(
            'status' => true,
            'message' => null,
            'result' => array()
        );

        /** @var $quiz \Quiz\Repository\Quiz */
        $quiz = $this->entityManager->getRepository('Quiz\Entity\Quiz');
        $status = $quiz->saveAnswersForQuiz($quizId, $this->getFacebookUserId(), $data);
        if (!$status) {
            $result['status'] = false;
            $result['message'] = 'Niezmiernie nam przykro, ale podczas zapisywaniu Twoich odpowiedzi serwer odmówił posłuszeństwa! Proszę zagraj pomownie, bez ograniczeń!';
        }

        return true;
    }

    public function canPlayAgain()
    {
        if (!$this->isAuth()) {
            return false;
        }

//        $ue = $this->getUserEntity();
//        if (!$ue) {
//            return false;
//        }

        /** @var $quiz \Quiz\Repository\Quiz */
        $quiz = $this->entityManager->getRepository('Quiz\Entity\Quiz');
        $result = $quiz->canPlayAgain($this->getUserId());

        if (isset($_GET['debug'])) {
            \Zend\Debug::dump(__METHOD__.__LINE__);
            \Zend\Debug::dump($result);
        }

        return $result;
    }

    public function userInviteFrients()
    {
        $result = array(
            'status' => false,
            'message' => null,
            'result' => array()
        );

        if (!$this->isAuth()) {
            $result['status'] = false;
            $result['message'] = 'Czy zalogowałeś się na Facebook\'a?';
            return $result;
        }

//        /** @var $user \Quiz\Repository\User */
//        $user = $this->entityManager->getRepository('Quiz\Entity\User');

//        var_dump($this->getUserId());
        $f = new \Quiz\Entity\FriendsInvite();
        $f->setUserId($this->getUserId());

        try {
            $this->entityManager->persist($f);
            $this->entityManager->flush();
            $result['status'] = (bool) $result;
        } catch(\Exception $e) {
            \Zend\Debug::dump($e->getMessage());
        }

        return $result;
    }
}
 
