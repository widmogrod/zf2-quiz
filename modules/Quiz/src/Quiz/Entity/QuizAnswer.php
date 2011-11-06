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
     * @ORM\ManyToOne(targetEntity="Quiz", cascade={"all"}, fetch="LAZY", inversedBy="answers")
     * @var Quiz
     */
    protected $quiz;

    /**
     * @ORM\ManyToOne(targetEntity="Answer", cascade={"all"}, fetch="LAZY")
     * @var Answer
     */
    protected $answer;

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
    }

    public function getQuiz()
    {
        return $this->quiz;
    }

    /**
     * @param \Quiz\Entity\Answer $answer
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;
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
}