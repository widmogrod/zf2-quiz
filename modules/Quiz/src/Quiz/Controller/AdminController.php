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
        $form = new Question();
        return array(
            'form' => $form
        );
    }
}
