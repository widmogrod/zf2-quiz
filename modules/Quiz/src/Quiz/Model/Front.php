<?php
namespace Quiz\Model;

use SpiffyDoctrine\Service\Doctrine;

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

    public function __construct(\Facebook $facebook, Doctrine $doctrine)
    {
        $this->facebook = $facebook;
        $this->entityManager = $doctrine->getEntityManager();
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
            \Zend\Debug::dump($e->getMessage());
            \Zend\Debug::dump($e->getFile());
            \Zend\Debug::dump($e->getLine());
            \Zend\Debug::dump($e->getTrace());
        }

        return false;
    }

    public function isAuth()
    {
        return $this->facebook->getUser() > 0;
    }

    public function getFacebookData()
    {
        $result = array();
        try {
            $result = $this->facebook->api('/me');
        } catch(\Exception $e) {
            \Zend\Debug::dump($e->getMessage());
            \Zend\Debug::dump($e->getFile());
            \Zend\Debug::dump($e->getLine());
            \Zend\Debug::dump($e->getTrace());
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

    public function getRandomQuestions()
    {
        /** @var $quiz \Quiz\Repository\Quiz */
        $quiz = $this->entityManager->getRepository('Quiz\Entity\Quiz');

        $questions = $quiz->getQuestions($this->getUserEntity());

        $result = array(
            'status' => true,
            'message' => null,
            'result' => $questions
        );

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
        if (!$status) {
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
}
 
