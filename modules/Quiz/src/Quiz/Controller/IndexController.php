<?php

namespace Quiz\Controller;

use Zend\Mvc\Controller\ActionController;
use Zend\Mvc\LocatorAware;

class IndexController extends ActionController implements LocatorAware
{
    public function indexAction()
    {
        /* @var $doctrine \SpiffyDoctrine\Service\Doctrine */
        $doctrine = $this->getLocator()->get('doctrine');
        /* @var $em \Doctrine\ORM\EntityManager */
        $em = $doctrine->getEntityManager();

        $user = new \Quiz\Entity\User();
        $user->setUsername('gabriel');

        try {
            $em->persist($user);
            $em->flush();
        } catch(\Exception $e) {
            var_dump($e->getMessage());
        }


        $records = $em->getRepository('\Quiz\Entity\User')->findAll();
        var_dump($records);

        return array();
    }
}
