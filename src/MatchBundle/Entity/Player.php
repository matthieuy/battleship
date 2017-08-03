<?php

namespace MatchBundle\Entity;

use BonusBundle\Bonus\BonusInterface;
use BonusBundle\BonusConstant;
use BonusBundle\Entity\Inventory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use UserBundle\Entity\User;

/**
 * Player
 *
 * @ORM\Table(name="players")
 * @ORM\Entity(repositoryClass="MatchBundle\Repository\PlayerRepository")
 */
class Player
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Game
     * @ORM\ManyToOne(targetEntity="MatchBundle\Entity\Game", inversedBy="players", fetch="EAGER")
     * @Gedmo\SortableGroup()
     */
    protected $game;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $name;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", cascade={"persist"})
     */
    protected $user;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    protected $ai;

    /**
     * @var int
     * @ORM\Column(type="smallint", length=2)
     */
    protected $life;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    protected $score;

    /**
     * @var string
     * @ORM\Column(type="string", length=6)
     */
    protected $color;

    /**
     * @var integer
     * @ORM\Column(type="smallint", length=1, nullable=true)
     */
    protected $team;

    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $boats;

    /**
     * @var ArrayCollection|BonusInterface[]
     * @ORM\OneToMany(targetEntity="BonusBundle\Entity\Inventory", mappedBy="player", cascade={"remove", "persist"})
     */
    protected $bonus;

    /**
     * @var int
     * @ORM\Column(type="smallint", length=1)
     * @Gedmo\SortablePosition()
     */
    protected $position;

    /**
     * @var integer
     * @ORM\Column(type="smallint")
     */
    protected $probability;

    /**
     * Player constructor.
     */
    public function __construct()
    {
        $this->ai = false;
        $this->score = 0;
        $this->life = 0;
        $this->probability = BonusConstant::INITIAL_PROBABILITY;
        $this->bonus = new ArrayCollection();
        $this->boats = [];
    }

    /**
     * Get id
     * @return int
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
     * Set game
     * @param Game $game
     * @return Player
     */
    public function setGame(Game $game)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get Position
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set position
     * @param int $position
     * @return Player
     */
    public function setPosition($position)
    {
        $this->position = intval($position);

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
     * Set user
     * @param User $user
     * @return Player
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        $this->name = $user->getUsername();
        $this->ai = $user->isAi();

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
     * Is player AI ?
     * @return boolean
     */
    public function isAi()
    {
        return $this->ai;
    }

    /**
     * Set ai
     * @param boolean $ai
     * @return Player
     */
    public function setAi($ai = true)
    {
        $this->ai = $ai;

        return $this;
    }

    /**
     * Get Life
     * @return int
     */
    public function getLife()
    {
        return $this->life;
    }

    /**
     * Set life
     * @param int $life
     * @return Player
     */
    public function setLife($life)
    {
        $this->life = $life;

        return $this;
    }

    /**
     * Remove one life
     * @return Player
     */
    public function removeLife()
    {
        $this->life--;

        return $this;
    }


    /**
     * Get Score
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Set score
     * @param int $score
     * @return Player
     */
    public function setScore($score)
    {
        $this->score = $score;

        return $this;
    }

    /**
     * Add points
     * @param integer $points
     *
     * @return Player
     */
    public function addScore($points)
    {
        $this->score += $points;

        return $this;
    }

    /**
     * Remove points
     * @param integer $points
     *
     * @return Player
     */
    public function removeScore($points)
    {
        $this->score -= $points;

        return $this;
    }

    /**
     * Get Color
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * Set color
     * @param string $color
     * @return Player
     */
    public function setColor($color)
    {
        $this->color = strtoupper($color);

        return $this;
    }

    /**
     * Get team
     * @return integer|null
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Set team
     * @param integer|null $team
     *
     * @return Player
     */
    public function setTeam($team = null)
    {
        $team = intval($team);
        $this->team = min(12, max(1, $team));

        return $this;
    }

    /**
     * Convert to array
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'userId' => $this->user->getId(),
            'ai' => $this->ai,
            'name' => $this->name,
            'position' => $this->position,
            'color' => $this->color,
            'team' => $this->team,
            'life' => $this->life,
            'score' => $this->score,
            'boats' => $this->getNumberOfBoat(),
        ];
    }

    /**
     * Get Boats
     * @return array
     */
    public function getBoats()
    {
        return $this->boats;
    }

    /**
     * Set boats
     * @param array $boats
     * @return Player
     */
    public function setBoats(array $boats)
    {
        $this->boats = $boats;

        return $this;
    }

    /**
     * Get list of boat by longer
     * @return array
     */
    public function getNumberOfBoat()
    {
        $list = [];
        if (is_array($this->boats)) {
            foreach ($this->boats as $boat) {
                if ($boat[1] > $boat[2]) {
                    if (isset($list[$boat[1]])) {
                        $list[$boat[1]]++;
                    } else {
                        $list[$boat[1]] = 1;
                    }
                }
            }
        }

        return $list;
    }

    /**
     * Get the probability to catch bonus
     * @return integer
     */
    public function getProbability()
    {
        return $this->probability;
    }

    /**
     * Set probability to catch bonus
     * @param integer $probability
     *
     * @return $this
     */
    public function setProbability($probability)
    {
        $this->probability = $probability;

        return $this;
    }

    /**
     * Add probability to catch bonus
     * @param integer $increment
     *
     * @return $this
     */
    public function addProbability($increment)
    {
        $this->probability += $increment;
        $this->probability = min($this->probability, 90);

        return $this;
    }

    /**
     * Get number of bonus
     * @return integer
     */
    public function getNbBonus()
    {
        return $this->bonus->count();
    }

    /**
     * Add bonus to inventory
     * @param Inventory $inventory
     *
     * @return $this
     */
    public function addBonus(Inventory $inventory)
    {
        $inventory->setPlayer($this);
        $this->bonus->add($inventory);

        return $this;
    }

    /**
     * Remove bonus
     * @param Inventory $inventory
     *
     * @return $this
     */
    public function removeBonus(Inventory $inventory)
    {
        $this->bonus->removeElement($inventory);

        return $this;
    }
}
