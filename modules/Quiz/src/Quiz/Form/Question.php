<?php
/**
 * @author gabriel
 */
 
namespace Quiz\Form;

use \TwitterBootstrap\Form as TwitterForm;

class Question extends TwitterForm\Form
{
    public function init()
    {
        $this->setMethod(self::METHOD_POST);
//        $this->setAction('/quizadmin/quizmanage');
        $this->setLegend('Pytanie');

        $this->addElement('text', 'title', array(
            'label' => 'Tytuł',
            'required' => true,
            'filters' => array(
                'StripTags',
            ),
            'validators' => array(
                array('StringLength', array('max' => 255))
            )
        ));

        $this->addElement('select', 'type', array(
            'label' => 'Rozaj pytania',
            'multiOptions' => \Quiz\Entity\Question::getAvailableTypes(),
            'required' => true
        ));

        $this->addElement('textarea', 'content', array(
            'label' => 'Treść pytania',
            'description' => 'Treść pytania zależy od rodzaju pytania',
            'required' => true,
            'filters' => array(
                'StripTags',
            ),
            'validators' => array(
                array('StringLength', array('max' => 255))
            )
        ));

        $this->initAnswers();

        $this->addActionElement('submit', 'save', array(
            'label' => 'Zapisz'
        ));
        $this->addActionElement('reset', 'cancel', array(
            'label' => 'Anuluj'
        ));
    }

    public function initAnswers()
    {
        $form = new TwitterForm\SubForm();
        $form->setLegend('Odpowiedzi');
        $form->addElement(self::ELEMENT_APPENDED_TEXT, '1', array(
            'label' => 'Odpowiedź pierwsza',
            'content' => '<input type="radio" name="correct" value="1">',
            'required' => true,
        ));
        $form->addElement(self::ELEMENT_APPENDED_TEXT, '2', array(
            'label' => 'Odpowiedź druga',
            'content' => '<input type="radio" name="correct" value="2">',
            'required' => true,
        ));
        $form->addElement(self::ELEMENT_APPENDED_TEXT, '3', array(
            'label' => 'Odpowiedź trzecia',
            'content' => '<input type="radio" name="correct" value="3">',
            'required' => true,
        ));

        $this->addSubForm($form, 'answers');
    }
}