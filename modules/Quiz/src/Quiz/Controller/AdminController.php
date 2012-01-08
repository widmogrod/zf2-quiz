<?php
namespace Quiz\Controller;

use Zend\Mvc\Controller\ActionController;;
use Quiz\Form\Question;

use DataGrid\DataGrid,
    DataGrid\Renderer\HtmlTable;

/**
 * @author Gabriel Habryn <gabriel.habryn@me.com>
 */
class AdminController extends ActionController
{
    public function quizlistAction()
    {
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getLocator()->get('doctrine_em');

        /* @var $repository \Quiz\Repository\Question */
        $repository = $em->getRepository('Quiz\Entity\Question');
        $q = $repository->getQueryQuestionList();

        $grid = DataGrid::factory($q);
        $grid->setSpecialColumn('menage', function ($row) {
            $url = sprintf('quizadmin/quizmanage?id=%d', $row['q_id']);
            $action1 = sprintf('<a href="%s" title="Edytuj pytanie">Edytuj</a>', $url);

            $url = sprintf('quizadmin/quizpreview?id=%d', $row['q_id']);
            $action2 = sprintf('<a href="%s" class="ajaxDialog" title="Podgląd pytania">Podgląd</a>', $url);

            return sprintf('%s<br/>%s', $action1, $action2);
        });
        $grid->setRenderer(new HtmlTable());

        return array(
            'grid' => $grid
        );
    }

    public function quizmanageAction()
    {
        /* @var $rq \Zend\Http\PhpEnvironment\Request */
        $rq = $this->getRequest();

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getLocator()->get('doctrine_em');
        /* @var $repository \Quiz\Repository\Question */
        $repository = $em->getRepository('Quiz\Entity\Question');

        $form = new Question();

        $result = array(
            'form' => $form,
            'isEdit' => false
        );

        if ($id = (int) $rq->query()->get('id'))
        {
            $data = $repository->getDataForForm($id);
            $form->populate($data);
            $result['isEdit'] = true;
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

    public function quizpreviewAction()
    {
        /* @var $rq \Zend\Http\PhpEnvironment\Request */
        $rq = $this->getRequest();

        $id = (int) $rq->query()->get('id');

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getLocator()->get('doctrine_em');
        /* @var $repository \Quiz\Repository\Question */
        $repository = $em->getRepository('Quiz\Entity\Question');
        return array(
            'data' => $repository->getDataForForm($id)
        );
    }

    public  function quizresultsAction()
    {
        /* @var $rq \Zend\Http\PhpEnvironment\Request */
        $rq = $this->getRequest();

        $time = strtotime($rq->query()->get('date','today'));
        $startDate = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) - date('N', $time) + 1, date('Y', $time)));
        $endDate = date('Y-m-d H:i:s', mktime(0,0,-1, date('m', $time), date('d', $time) - (date('N', $time) - 8), date('Y', $time)));
        $limit = 200;

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getLocator()->get('doctrine_em');

        /* @var $repository \Quiz\Repository\Quiz */
        $repository = $em->getRepository('Quiz\Entity\Quiz');
        $q = $repository->getResultQuery($startDate, $endDate, $limit, true);

        $grid = DataGrid::factory($q);
        $grid->setRenderer(new HtmlTable());
        $grid->setSpecialColumn('facebookId', array(
            DataGrid::CELL => function ($row) {
                return sprintf('<img src="https://graph.facebook.com/%s/picture" >', $row['facebookId']);
            },
            DataGrid::COLUMN => array(
                'name' => 'avatar',
                'attribs' => array(
                    'width' => '50'
                )
            )
        ));

        return array(
            'grid' => $grid,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'timeDate' => date('Y-m-d', $time)
        );
    }

    public function quizusersAction()
    {
        /* @var $rq \Zend\Http\PhpEnvironment\Request */
        $rq = $this->getRequest();

        /*
         * Date range validation
         */
        {{
            $time = strtotime($rq->query()->get('date','today'));
            $startDate = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) - date('N', $time) + 1, date('Y', $time)));
            $endDate = date('Y-m-d', mktime(0,0,-1, date('m', $time), date('d', $time) - (date('N', $time) - 8), date('Y', $time)));

            $startDate = $rq->query()->get('startDate', $startDate);
            $startDate = strtotime($startDate);
            $startDate = date('Y-m-d', $startDate);

            $endDate   = $rq->query()->get('end Date', $endDate);
            $endDate   = strtotime($endDate);
            $endDate = date('Y-m-d', $endDate);
        }}

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getLocator()->get('doctrine_em');

        /* @var $repository \Quiz\Repository\User */
        $repository = $em->getRepository('Quiz\Entity\User');
        $q = $repository->getQueryUserSummaryList($startDate, $endDate);


        $grid = DataGrid::factory($q);
        $grid->setSpecialColumn('avatar', array(
            DataGrid::CELL => function ($row) {
                return sprintf('<img src="https://graph.facebook.com/%s/picture" >', $row['avatar']);
            },
            DataGrid::COLUMN => array(
                'name' => 'avatar',
                'attribs' => array(
                    'width' => '50'
                )
            )
        ));
        $grid->setSpecialColumn('email', function ($row) {
            return sprintf('<span class="span3" ww>%s</span>', $row['email']);
        });
        $grid->setSpecialColumn('fullname', function ($row) {
            return sprintf('<span class="span2">%s</span>', $row['fullname']);
        });
        $grid->setSpecialColumn('show_question', function ($row) {
            $url = sprintf('quizadmin/quizuserquestion?id=%d', $row['id']);
            $action1 = sprintf('<a href="%s">Statystyka odpowiedzi na pytania</a>', $url);

            $url = sprintf('quizadmin/quizusercleartodayplay?id=%d', $row['id']);
            $action2 = sprintf('<a href="%s">Resetuj dzisiejszą rozgrywkę</a>', $url);

            return sprintf('%s<br/>%s', $action1, $action2);
        });
        $grid->setRenderer(new HtmlTable());

        return array(
            'grid' => $grid,
            'startDate' => $startDate,
            'endDate' => $endDate
        );
    }

    public function quizuserquestionAction()
    {
        /* @var $rq \Zend\Http\PhpEnvironment\Request */
        $rq = $this->getRequest();

        $userId = $rq->query()->get('id');

        /*
         * Date range validation
         */
        {{
            $time = strtotime($rq->query()->get('date','today'));
            $startDate = date('Y-m-d', mktime(0,0,0, date('m', $time), date('d', $time) - date('N', $time) + 1, date('Y', $time)));
            $endDate = date('Y-m-d', mktime(0,0,-1, date('m', $time), date('d', $time) - (date('N', $time) - 8), date('Y', $time)));

            $startDate = $rq->query()->get('startDate', $startDate);
            $startDate = strtotime($startDate);
            $startDate = date('Y-m-d', $startDate);

            $endDate   = $rq->query()->get('end Date', $endDate);
            $endDate   = strtotime($endDate);
            $endDate = date('Y-m-d', $endDate);
        }}

        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getLocator()->get('doctrine_em');

        /* @var $repository \Quiz\Repository\Question */
        $repository = $em->getRepository('Quiz\Entity\Question');
        $q = $repository->getQueryStatisticListForUser($userId, $startDate, $endDate);

        $grid = DataGrid::factory($q);
        $grid->setRenderer(new HtmlTable());

        return array(
            'grid' => $grid,
            'startDate' => $startDate,
            'endDate' => $endDate
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
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $this->getLocator()->get('doctrine_em');

        $userId = $rq->query()->get('id');

        /** @var $quiz \Quiz\Repository\Quiz */
        $quiz = $em->getRepository('Quiz\Entity\Quiz');
        $quiz->clearTodayUserPlay($userId);

        $this->plugin('redirect')->toUrl($_SERVER['HTTP_REFERER']);
    }
}
