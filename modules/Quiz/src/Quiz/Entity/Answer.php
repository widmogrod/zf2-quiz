<?php
/**
 * @author gabriel
 */
 
namespace Quiz\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz_answer")
 */
class Answer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Question", cascade={"persist"}, fetch="LAZY", inversedBy="answers")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     * @var Question
     */
    protected $question;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $question_id;

    /**
     * @ORM\Column(type="string", length=100, nullable=false)
     */
    protected $name;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $isCorrect;

    public function getId()
    {
        return $this->id;
    }

    public function setQuestion(Question $question, $addToInverse = true)
    {
        if ($addToInverse) {
            $question->addAnswer($this, false);
        }
        $this->question = $question;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function setIsCorrect($isCorrect)
    {
        $this->isCorrect = (bool) $isCorrect;
    }

    public function getIsCorrect()
    {
        return $this->isCorrect;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function toArray()
    {
        return array(
            'id' => $this->id,
            'name' => $this->name,
            'isCorrect' => $this->isCorrect,
        );
    }

    /*
     * Methods for FKs
     */

    public function setQuestionId($question_id)
    {
        $this->question_id = $question_id;
    }

    public function getQuestionId()
    {
        return $this->question_id;
    }
}