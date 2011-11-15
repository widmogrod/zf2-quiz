<?php
/**
 * @author gabriel
 */
 
namespace Quiz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="Quiz\Repository\User")
 * @ORM\Table(name="quiz_user")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    protected $username;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    protected $fullname;

    /**
     * @ORM\OneToMany(targetEntity="Quiz", mappedBy="user", cascade={"persist","remove"}, orphanRemoval=true)
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $quizzes;

    /**
     * @ORM\Column(type="bigint", nullable=false, unique=true)
     */
    protected $facebookId;

    public function getId()
    {
        return $this->id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        $this->quizzes = new ArrayCollection();
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function addQuiz(Quiz $quiz, $setToOwner = true)
    {
        if ($setToOwner) {
            $quiz->setUser($this, false);
        }
        $this->quizzes->add($quiz);
    }

    public function getQuizzes()
    {
        return $this->quizzes;
    }

    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }

    public function getFacebookId()
    {
        return $this->facebookId;
    }

    public function setFullname($fullname)
    {
        $this->fullname = $fullname;
    }

    public function getFullname()
    {
        return $this->fullname;
    }
}