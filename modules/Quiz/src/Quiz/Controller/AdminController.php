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

        $dql = 'SELECT q FROM Quiz\Entity\Question q';
        /* @var $q \Doctrine\ORM\Query */
        $q = $em->createQuery($dql);

        $grid = DataGrid::factory($q);
        $grid->setSpecialColumn('edit', function ($row) {
            $url = sprintf('/quizadmin/quizmanage?id=%d', $row['id']);
            return sprintf('<a href="%s">Edytuj</a>', $url);
        });
        $grid->setRenderer(new HtmlTable());

        return array(
            'grid' => $grid
        );
    }

}
