<?php
namespace Quiz\Controller;

use Zend\Mvc\Controller\ActionController;;
use Quiz\Form\Question;

use DataGrid\DataGrid;
use DataGrid\Renderer\HtmlTable;

/**
 * @author Gabriel Habryn <gabriel.habryn@me.com>
 */
class AdminController extends ActionController
{
    public function quizmanageAction()
    {
        /* @var $rq \Zend\Http\PhpEnvironment\Request */
        $rq = $this->getRequest();

        /* @var $doctrine \SpiffyDoctrine\Service\Doctrine */
        $doctrine = $this->getLocator()->get('doctrine');
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $doctrine->getEntityManager();
        /* @var $repository \Quiz\Repository\Question */
        $repository = $em->getRepository('Quiz\Entity\Question');

        $form = new Question();

        $result = array(
            'form' => $form
        );

        if ($id = (int) $rq->query()->get('id')) {
            $data = $repository->getDataForForm($id);
            $form->populate($data);
        }

        if (!$rq->isPost()) {
            return $result;
        }

        $data = $rq->post()->toArray();
        if (!$form->isValid($data)) {
            return $result;
        }
        $data = $form->getValues();

        if ($id) {
            $repository->update($data, $id);
        } else {
            $repository->create($data);
        }

        $this->plugin('redirect')->toRoute('default', array(
            'controller' => 'quizadmin',
            'action' => 'quizlist'
        ));

        return $result;
    }

    public function quizlistAction()
    {
        /* @var $doctrine \SpiffyDoctrine\Service\Doctrine */
        $doctrine = $this->getLocator()->get('doctrine');
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $doctrine->getEntityManager();

        /* @var $repository \Quiz\Repository\Question */
        $repository = $em->getRepository('Quiz\Entity\Question');

        $dql = 'SELECT q, (SELECT a.name FROM Quiz\Entity\Answer a WHERE a.question = q.id AND a.isCorrect = true) AS correct_answer FROM Quiz\Entity\Question q ORDER BY q.id DESC';
        /* @var $q \Doctrine\ORM\Query */
        $q = $em->createQuery($dql);

        $grid = DataGrid::factory($q);
        $grid->setSpecialColumn('edit', function ($row) {
            $url = sprintf('quizadmin/quizmanage?id=%d', $row['q_id']);
            return sprintf('<a href="%s">Edytuj</a>', $url);
        });
        $grid->setRenderer(new HtmlTable());

        return array(
            'grid' => $grid
        );
    }

    public function quizusersAction()
    {
        /* @var $doctrine \SpiffyDoctrine\Service\Doctrine */
        $doctrine = $this->getLocator()->get('doctrine');
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $doctrine->getEntityManager();

        /* @var $repository \Quiz\Repository\Question */
        $repository = $em->getRepository('Quiz\Entity\Question');

        $startDate = date('Y-m-d', mktime(0,0,0, date('m'), date('d') - date('N') + 1, date('Y')));
        $endDate = date('Y-m-d', mktime(0,0,0, date('m'), date('d') - (date('N') - 7), date('Y')));

        $points = 'SELECT SUM(ap.second) * 10 FROM Quiz\Entity\Quiz qp '.
                   'JOIN qp.user up '.
                   'JOIN qp.answers ap '.
                   'JOIN ap.answer aap '.
                   'WHERE qp.date BETWEEN :startData AND :endDate '.
                   'AND qp.isClose = true AND aap.isCorrect = true '.
                   'AND up.id = u.id';

        $dql = 'SELECT u.facebookId as avatar, u.id, u.fullname, u.email, '.
                    '(%s) AS points, '.
                    '(SELECT COUNT(q.id) FROM Quiz\Entity\Quiz q WHERE q.user_id = u.id) AS play_count, '.
                    '(SELECT COUNT(fi.id) FROM Quiz\Entity\FriendsInvite fi WHERE fi.userId = u.id) AS invited_friend '.
               'FROM Quiz\Entity\User u ORDER BY points DESC';

        $dql = sprintf($dql, $points);

        /* @var $q \Doctrine\ORM\Query */
        $q = $em->createQuery($dql);
        $q->setParameter('startData', $startDate);
        $q->setParameter('endDate', $endDate);

        $grid = DataGrid::factory($q);
        $grid->setSpecialColumn('avatar', function($row) {
             return sprintf('<img src="https://graph.facebook.com/%s/picture" >', $row['avatar']);
        });
        $grid->setSpecialColumn('show_question', function ($row) {
            $url = sprintf('quizadmin/quizuserquestion?id=%d', $row['id']);
            return sprintf('<a href="%s">Pokaż pytania</a>', $url);
        });
        $grid->setSpecialColumn('clear_today_play', function ($row) {
            $url = sprintf('quizadmin/quizusercleartodayplay?id=%d', $row['id']);
            return sprintf('<a href="%s">Resetuj dzisiejszą rozgrywkę</a>', $url);
        });

        $grid->setRenderer(new HtmlTable());

        return array(
            'grid' => $grid
        );
    }

    public function quizuserquestionAction()
    {
        /* @var $rq \Zend\Http\PhpEnvironment\Request */
        $rq = $this->getRequest();

        /* @var $doctrine \SpiffyDoctrine\Service\Doctrine */
        $doctrine = $this->getLocator()->get('doctrine');
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $doctrine->getEntityManager();

        $userId = $rq->query()->get('id');
        $startDate = date('Y-m-d', mktime(0,0,0, date('m'), date('d') - date('N') + 1, date('Y')));
        $endDate = date('Y-m-d', mktime(0,0,0, date('m'), date('d') - (date('N') - 7), date('Y')));

        // quiestions answered by user
        $sub = 'SELECT COUNT(a.question_id) FROM Quiz\Entity\QuizAnswer qa JOIN qa.answer a JOIN qa.quiz z WHERE z.user_id = :userId AND z.date BETWEEN :startData AND :endDate';
        // count how offen this quiestion was answered
        $answers = 'SELECT COUNT(qaa.id) FROM Quiz\Entity\QuizAnswer qaa JOIN qaa.answer aa WHERE aa.question_id = q.id';
        // sort quiestion by less used and last answered by user
        $dql = 'SELECT q, (%s) AS top_answers, (%s) AS user_answers FROM Quiz\Entity\Question q  ORDER BY top_answers ASC, user_answers ASC ';
        // one dql to bind them all
        $dql = sprintf($dql, $answers, $sub);

        /** @var $q  \Doctrine\ORM\Query */
        $q = $em->createQuery($dql);
        $q->setParameter('userId', $userId, \Doctrine\DBAL\Types\Type::INTEGER);
        $q->setParameter('startData', $startDate);
        $q->setParameter('endDate', $endDate);
//        $q->setMaxResults(10);

        $grid = DataGrid::factory($q);
        $grid->setRenderer(new HtmlTable());

        return array(
            'grid' => $grid
        );
    }

    /**
     * Clear today play for user.
     * This action is only for testing purpose.
     * @return void
     */
    public function quizusercleartodayplayAction()
    {
        /* @var $rq \Zend\Http\PhpEnvironment\Request */
        $rq = $this->getRequest();
        /* @var $doctrine \SpiffyDoctrine\Service\Doctrine */
        $doctrine = $this->getLocator()->get('doctrine');
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $doctrine->getEntityManager();

        $userId = $rq->query()->get('id');

        /** @var $quiz \Quiz\Repository\Quiz */
        $quiz = $em->getRepository('Quiz\Entity\Quiz');
        $quiz->clearTodayUserPlay($userId);

        $this->plugin('redirect')->toUrl($_SERVER['HTTP_REFERER']);
    }
}
