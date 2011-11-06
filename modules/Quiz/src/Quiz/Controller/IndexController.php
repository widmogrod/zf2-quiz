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

//        $user = new \Quiz\Entity\User();
//        $user->setUsername('gabriel');
//
//        try {
//            $em->persist($user);
////            $em->flush();
//        } catch(\Exception $e) {
//            var_dump($e->getMessage());
//        }
//
//        $question = new \Quiz\Entity\Question();
//        $question->setTitle('pierwsze pytanie');
//        $question->setContent('Jak masz na imie?');
//
//
//        $answer = new \Quiz\Entity\Answer();
//        $answer->setName('Gabriel');
//        $answer->setIsCorrect(true);
//        $question->addAnswer($answer);
//
//        $answer = new \Quiz\Entity\Answer();
//        $answer->setName('Rafał');
//        $answer->setIsCorrect(false);
//        $question->addAnswer($answer);
//
//        $answer = new \Quiz\Entity\Answer();
//        $answer->setName('Michał');
//        $answer->setIsCorrect(false);
//        $question->addAnswer($answer);
//
//        try {
//            $em->persist($question);
//            $em->flush();
//        } catch(\Exception $e) {
//            var_dump($e->getMessage());
//        }
//
//        $records = $em->getRepository('\Quiz\Entity\User')->findAll();
//        var_dump($records);
//
//        $records = $em->getRepository('\Quiz\Entity\Question')->findAll();
//        var_dump($records);
//
//        $records = $em->getRepository('\Quiz\Entity\Answer')->findAll();
//        var_dump($records);

        $dql = 'SELECT q.*, a.* FROM \Quiz\Entity\Question q JOIN \Quiz\Entity\Answer a';
        /* @var $q \Doctrine\ORM\Query */
        $q = $em->createQuery($dql);

        try {
            $result = $q->getResult();

            var_dump($result);
            var_dump($q);
        } catch(\Exception $e) {
            var_dump($e->getMessage());
        }

        return array();
    }
}
