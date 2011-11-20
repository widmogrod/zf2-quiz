<?php
namespace Quiz\Controller;

use Zend\Mvc\Controller\ActionController;;
use Quiz\Form\Question;

use DataGrid\DataGrid;
use DataGrid\Renderer\HtmlTable;
use Doctrine\ORM\Query\ResultSetMapping;

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
        $endDate = date('Y-m-d H:i:s', mktime(0,0,-1, date('m'), date('d') - (date('N') - 8), date('Y')));


//        echo $startDate;
//        echo $endDate;

        /*
         * Creating nice query ;)
         */
        {{
            $rsm = new ResultSetMapping;
            $rsm->addScalarResult('points', 'points');
            $rsm->addScalarResult('play_count', 'play_count');
            $rsm->addScalarResult('invited_friend', 'invited_friend');
            $rsm->addScalarResult('avatar', 'avatar');
            $rsm->addScalarResult('id', 'id');
            $rsm->addScalarResult('email', 'email');
            $rsm->addScalarResult('fullname', 'fullname');

//            $rsm->addEntityResult('Quiz\Entity\User', 'u');
//            $rsm->addFieldResult('u', 'id', 'id');
//            $rsm->addFieldResult('u', 'email', 'email');
//            $rsm->addFieldResult('u', 'fullname', 'fullname');

            $sql = 'SELECT q0_.facebookId AS avatar, q0_.id, q0_.fullname, q0_.email,

                        (
                            SELECT SUM(q1_.second) * 10 FROM quiz_quiz q2_
                            INNER JOIN quiz_user q3_ ON q2_.user_id = q3_.id
                            INNER JOIN quiz_quiz_answer q1_ ON q2_.id = q1_.quiz_id
                            INNER JOIN quiz_answer q4_ ON q1_.answer_id = q4_.id
                            WHERE q2_.date BETWEEN :startDate AND :endDate
                            AND q2_.isClose = true AND q4_.isCorrect = true
                            AND q3_.id = q0_.id
                            GROUP BY q2_.id
                            ORDER BY 1 DESC
                            LIMIT 1
                        ) AS points,

                        (
                            SELECT COUNT(q5_.id) AS dctrn__2 FROM quiz_quiz q5_
                            WHERE q5_.user_id = q0_.id
                        ) AS play_count,

                        (
                            SELECT COUNT(q6_.id) AS dctrn__3 FROM quiz_friend_invite q6_
                            WHERE q6_.userId = q0_.id
                        ) AS invited_friend

                    FROM quiz_user q0_ ORDER BY points DESC NULLS LAST';

            /** @var $q \Doctrine\ORM\NativeQuery */
            $q = $em->createNativeQuery($sql, $rsm);

//            echo $q->getSQL();
        }}

        /*
         * Previos DQL statment, is not valid becouse not support LIMIT statment in SUB-SELECT 'points'
         * Remain for educational purpose.
         */
        {{
//            $points = 'SELECT SUM(ap.second) * 10 FROM Quiz\Entity\Quiz qp '.
//                       'JOIN qp.user up '.
//                       'JOIN qp.answers ap '.
//                       'JOIN ap.answer aap '.
//                       'WHERE qp.date BETWEEN :startDate AND :endDate '.
//                       'AND qp.isClose = true AND aap.isCorrect = true '.
//                       'AND up.id = u.id';
//
//            $dql = 'SELECT u.facebookId as avatar, u.id, u.fullname, u.email, '.
//                        '(%s) AS points, '.
//                        '(SELECT COUNT(q.id) FROM Quiz\Entity\Quiz q WHERE q.user_id = u.id) AS play_count, '.
//                        '(SELECT COUNT(fi.id) FROM Quiz\Entity\FriendsInvite fi WHERE fi.userId = u.id) AS invited_friend '.
//                   'FROM Quiz\Entity\User u ORDER BY points DESC';
//
//            $dql = sprintf($dql, $points);
//
//            /* @var $q \Doctrine\ORM\Query */
//            $q = $em->createQuery($dql);
        }}

        $q->setParameter('startDate', $startDate);
        $q->setParameter('endDate', $endDate);
//        \Zend\Debug::dump($q->getParameters());

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
