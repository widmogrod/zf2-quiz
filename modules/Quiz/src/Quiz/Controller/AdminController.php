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

        $form = new Question();

        $result = array(
            'form' => $form
        );

        if (!$rq->isPost()) {
            return $result;
        }

        /* @var $doctrine \SpiffyDoctrine\Service\Doctrine */
        $doctrine = $this->getLocator()->get('doctrine');
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $doctrine->getEntityManager();

        if (!$form->isValid($rq->post()->toArray())) {
            return $result;
        }

        /* @var $repository \Quiz\Repository\Question */
        $repository = $em->getRepository('Quiz\Entity\Question');
        $repository->create($rq->post()->toArray());

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

        $dql = 'SELECT q FROM Quiz\Entity\Question q JOIN q.answers';
        /* @var $q \Doctrine\ORM\Query */
        $q = $em->createQuery($dql);

        $grid = DataGrid::factory($q);
        $grid->setRenderer(new HtmlTable());

        return array(
            'grid' => $grid
        );
    }

}
