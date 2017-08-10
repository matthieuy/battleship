<?php

namespace BonusBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use MatchBundle\Entity\Game;
use MatchBundle\Entity\Player;

/**
 * Inventory
 * @ORM\Table(name="inventory")
 * @ORM\Entity(repositoryClass="BonusBundle\Repository\InventoryRepository")
 * @ORM\EntityListeners({"BonusBundle\EventListener\InventoryEntityListener"})
 */
class Inventory
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
     */
    protected $game;

    /**
     * @var Player
     * @ORM\ManyToOne(targetEntity="MatchBundle\Entity\Player", inversedBy="bonus")
     */
    protected $player;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $useIt;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $options;

    /**
     * Inventory constructor.
     */
    public function __construct()
    {
        $this->useIt = false;
        $this->options = [];
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
     * Get game
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Get player
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set player
     * @param Player $player
     *
     * @return $this
     */
    public function setPlayer(Player $player)
    {
        $this->game = $player->getGame();
        $this->player = $player;

        return $this;
    }

    /**
     * Get the name of the bonus
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name of the bonus
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
     * Set in use
     * @param boolean $use
     *
     * @return $this
     */
    public function setUse($use = true)
    {
        $this->useIt = $use;

        return $this;
    }

    /**
     * Use it in game
     * @return boolean
     */
    public function isUseIt()
    {
        return $this->useIt;
    }

    /**
     * Get options
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set options
     * @param array $options
     *
     * @return $this
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Add a option
     * @param string $name  Name of the option
     * @param mixed  $value Value
     *
     * @return $this
     */
    public function addOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Get option
     * @param string $name The option name
     *
     * @return mixed|null The option value or null
     */
    public function getOption($name)
    {
        if ($this->hasOption($name)) {
            return $this->options[$name];
        }

        return null;
    }

    /**
     * Is the option exist
     * @param string $name
     *
     * @return boolean
     */
    public function hasOption($name)
    {
        return array_key_exists($name, $this->options);
    }

    /**
     * Convert to array
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'uniq' => $this->name,
            'use' => $this->useIt,
            'options' => $this->options,
        ];
    }
}
