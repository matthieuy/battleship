<?php

namespace NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MatchBundle\Entity\Game;
use UserBundle\Entity\User;

/**
 * Notification
 *
 * @ORM\Table(name="notification")
 * @ORM\Entity(repositoryClass="NotificationBundle\Repository\NotificationRepository")
 */
class Notification
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Game
     * @ORM\ManyToOne(targetEntity="MatchBundle\Entity\Game")
     * @ORM\JoinColumn(name="game_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $game;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $user;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $enabled;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $configuration;

    /**
     * Notification constructor.
     * @param string|null $name
     * @param Game|null   $game
     * @param User|null   $user
     */
    public function __construct($name = null, Game $game = null, User $user = null)
    {
        $this->configuration = [];
        if ($name) {
            $this->name = $name;
        }
        if ($game) {
            $this->game = $game;
        }
        if ($user) {
            $this->user = $user;
        }
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
     * Get User
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set User
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Enabled
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set Enabled
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get Configuration
     * @return array
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * Get specific configuration
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getConfigurationValue($name, $default)
    {
        return (key_exists($name, $this->configuration)) ? $this->configuration[$name] : $default;
    }

    /**
     * Set Configuration
     * @param array $configuration
     *
     * @return $this
     */
    public function setConfiguration(array $configuration)
    {
        $this->configuration = $configuration;

        return $this;
    }

    /**
     * Add a configuration
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function addConfiguration($name, $value)
    {
        $this->configuration[$name] = $value;

        return $this;
    }

    /**
     * Remove a configuration value
     * @param string $name
     *
     * @return $this
     */
    public function removeConfiguration($name)
    {
        if (key_exists($name, $this->configuration)) {
            unset($this->configuration[$name]);
        }

        return $this;
    }
}
