<?php
/**
 * @author gabriel
 */
 
namespace Quiz\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="quiz_question")
 */
class Question
{
    const TYPE_TEXT  = 'text';
    const TYPE_AUDIO = 'audio';
    const TYPE_VIDEO = 'video';
    const TYPE_IMAGE = 'image';

    protected $availableTypes = array(
        self::TYPE_AUDIO,
        self::TYPE_IMAGE,
        self::TYPE_VIDEO,
        self::TYPE_TEXT,
    );

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\generatedValue(strategy="AUTO")
     */
    protected $id = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $title;

    /**
     * @ORM\Column(type="string", length=10, nullable=false)
     */
    protected $type = self::TYPE_TEXT;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $content;

    /**
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question", cascade={"persist", "remove", "merge"}, orphanRemoval=true)
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setContent($content)
    {
        $this->content = $content;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setType($type)
    {
        if (!isset($this->availableTypes[$type]))
        {
            $message = 'Invalid question type "%s". Available types: %s';
            $message = sprintf($message, $type, implode(', ', $this->availableTypes));
            throw new \InvalidArgumentException($message);
        }

        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    public function addAnswer(Answer $answer)
    {
        if (!$this->answers->contains($answer))
        {
            $this->answers->add($answer);
            $answer->setQuestion($this);
        }
    }
}