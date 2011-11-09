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
        /*
         * Validation for "<input type="radio" name="correct" value="3">"
         * defined in answer
         */
        $this->addElement('hidden', 'correct', array(
            'required' => true,
//            'validators' => array(
//                array('NotEmpty', array('breakChainOnFailure' => true, 'message' => array(\Zend\Validator\NotEmpty::IS_EMPTY => "Zaznacz przy odpowiedzi, która z nich jest prawidłowa",)))
//            )
        ));
//        /** @var $validator  \Zend\Validator\NotEmpty */
//        $validator = $this->getElement('correct')->getValidators('Zend\Validator\NotEmpty');
//        $validator->setMessage("Zaznacz przy odpowiedzi, która z nich jest prawidłowa", \Zend\Validator\NotEmpty::IS_EMPTY);

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

    public function isValid($data)
    {
        if (isset($data['correct'])) {
            $this->changeAnswerAdditionalElementToCheckedCheckbox($data['correct']);
        }

        return parent::isValid($data);
    }

    public function populate(array $values)
    {
        if (isset($values['correct'])) {
            $this->changeAnswerAdditionalElementToCheckedCheckbox($values['correct']);
            unset($values['correct']);
        }

        return parent::populate($values);
    }

    private function changeAnswerAdditionalElementToCheckedCheckbox($elementName)
    {
        $element = $this->getSubForm('answers')->getElement($elementName);
        if ($element instanceof \Zend\Form\Element)
        {
            $element->setAttrib('content', sprintf('<input type="radio" name="correct" value="%s" checked>', $element->getName()));
        }
    }
}