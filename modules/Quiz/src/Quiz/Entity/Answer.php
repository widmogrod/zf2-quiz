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
     * @ORM\ManyToOne(targetEntity="Question", cascade={"all"}, fetch="LAZY", inversedBy="answers")
     * @var Question
     */
    protected $question;

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
}