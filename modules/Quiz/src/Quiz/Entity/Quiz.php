<?php
/**
 * @author gabriel
 */
 
namespace Quiz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Quiz\Repository\Quiz")
 * @ORM\Table(name="quiz_quiz")
 */
class Quiz
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="User", cascade={"persist"}, fetch="LAZY", inversedBy="quizzes")
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="QuizAnswer", mappedBy="quiz", cascade={"persist"}, orphanRemoval=true)
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $answers;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $date;

    public function __construct()
    {
        $this->date = new \DateTime('now');
        $this->answers = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setUser(User $user, $addToInverse = true)
    {
        if ($addToInverse) {
            $user->addQuiz($this, false);
        }
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function addAnswer(QuizAnswer $answer, $setToOwner = true)
    {
        if ($setToOwner) {
            $answer->setQuiz($this, false);
        }
        $this->answers->add($answer);
    }

    public function getAnswers()
    {
        return $this->answers;
    }
}