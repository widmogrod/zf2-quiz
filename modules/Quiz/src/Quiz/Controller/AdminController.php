<?php
namespace Quiz\Controller;

use Zend\Mvc\Controller\ActionController;;
use Quiz\Form\Question;

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
}
