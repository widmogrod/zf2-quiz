<?php
/**
 * @author gabriel
 */
 
namespace Quiz\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz_quiz_answer")
 */
class QuizAnswer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="Quiz", cascade={"persist"}, fetch="LAZY", inversedBy="answers")
     * @ORM\JoinColumn(name="quiz_id", referencedColumnName="id")
     * @var Quiz
     */
    protected $quiz;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $quiz_id;

    /**
     * Unidirectional
     * @ORM\ManyToOne(targetEntity="Answer", cascade={"persist"}, fetch="LAZY")
     * @ORM\JoinColumn(name="answer_id", referencedColumnName="id")
     * @var Answer
     */
    protected $answer;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $answer_id;

    /**
     * @ORM\Column(type="smallint")
     * @var integer
     */
    protected $second;

    public function getId()
    {
        return $this->id;
    }

    public function setQuiz(Quiz $quiz, $addToInverse = true)
    {
        if ($addToInverse) {
            $quiz->addAnswer($this, false);
        }
        $this->quiz = $quiz;
        $this->setQuizId($quiz->getId());
    }

    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * @param \Quiz\Entity\Answer $answer
     */
    public function setAnswer(Answer $answer)
    {
        $this->answer = $answer;
        $this->setAnswerId($answer->getId());
    }

    /**
     * @return \Quiz\Entity\Answer
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    /**
     * @param int $second
     */
    public function setSecond($second)
    {
        $this->second = abs((int) $second);
    }

    /**
     * @return int
     */
    public function getSecond()
    {
        return $this->second;
    }

    /*
     * Methods for FKs
     */

    public function setAnswerId($answer_id)
    {
        $this->answer_id = $answer_id;
    }

    public function getAnswerId()
    {
        return $this->answer_id;
    }

    public function setQuizId($quiz_id)
    {
        $this->quiz_id = $quiz_id;
    }

    public function getQuizId()
    {
        return $this->quiz_id;
    }
}