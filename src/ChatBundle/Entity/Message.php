<?php

namespace ChatBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MatchBundle\Entity\Game;
use UserBundle\Entity\User;

/**
 * Message
 *
 * @ORM\Table(name="chat_message")
 * @ORM\Entity(repositoryClass="ChatBundle\Repository\MessageRepository")
 */
class Message
{
    const CHANNEL_GLOBAL = 0;
    const CHANNEL_TEAM = 1;
    const CHANNEL_PRIVATE = 2;

    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User|null User or system
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=true)
     */
    protected $author;

    /**
     * @var Game
     * @ORM\ManyToOne(targetEntity="MatchBundle\Entity\Game")
     */
    protected $game;

    /**
     * @var integer
     * @ORM\Column(type="smallint")
     */
    protected $channel;

    /**
     * @var integer|null
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $recipient;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    protected $text;

    /**
     * @var null|array
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $context;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $date;

    /**
     * Message constructor.
     */
    public function __construct()
    {
        $this->author = null;
        $this->date = new \DateTime('now');
        $this->channel = self::CHANNEL_GLOBAL;
        $this->context = null;
    }

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get Author
     * @return null|User
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Set Author
     * @param null|User $author
     *
     * @return $this
     */
    public function setAuthor(User $author = null)
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Get Game
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Set Game
     * @param Game $game
     *
     * @return $this
     */
    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get Channel
     * @return integer
     */
    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * Set Channel
     * @param integer $channel
     *
     * @return $this
     */
    public function setChannel($channel)
    {
        $this->channel = $channel;

        return $this;
    }

    /**
     * Get Recipient
     * @return integer|null
     */
    public function getRecipient()
    {
        return $this->recipient;
    }

    /**
     * Set Recipient
     * @param integer|null $recipient
     *
     * @return $this
     */
    public function setRecipient($recipient)
    {
        $this->recipient = $recipient;

        return $this;
    }

    /**
     * Get Text
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set Text
     * @param string $text
     *
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get Context
     * @return array|null
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set Context
     * @param array|null $context
     *
     * @return $this
     */
    public function setContext(array $context = null)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get Date
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set Date
     * @param \DateTime $date
     *
     * @return $this
     */
    public function setDate(\DateTime $date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Convert to string
     * @return string
     */
    public function __toString()
    {
        return $this->text;
    }

    /**
     * Convert to array
     * @return array
     */
    public function toArray()
    {
        $infos = [
            'id' => $this->id,
            'game' => $this->game->getId(),
            'text' => $this->text,
            'timestamp' => $this->date->getTimestamp(),
        ];

        if ($this->author !== null) {
            $infos['username'] = $this->author->getUsername();
        } elseif ($this->context !== null) {
            $infos['context'] = $this->context;
        }

        if ($this->channel !== self::CHANNEL_GLOBAL) {
            $infos['recipient'] = $this->recipient;
            $infos['channel'] = $this->channel;
        }

        return $infos;
    }
}
