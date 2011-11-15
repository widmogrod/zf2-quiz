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
     * @var \Quiz\Entity\User
     */
    protected $user;

    /**
     * @ORM\OneToMany(targetEntity="QuizAnswer", mappedBy="quiz", cascade={"persist"}, orphanRemoval=true)
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $answers;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    protected $date;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $isClose;

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

    /**
     * @return \Quiz\Entity\User
     */
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

    public function setIsClose($isClose)
    {
        $this->isClose = $isClose;
    }

    public function getIsClose()
    {
        return $this->isClose;
    }

    public function setDate(\DateTime $date)
    {
        $this->date = $date;
    }

    public function getDate()
    {
        return $this->date;
    }
}