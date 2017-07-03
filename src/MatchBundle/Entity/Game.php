<?php

namespace MatchBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use MatchBundle\Box\Box;
use Symfony\Component\Validator\Constraints as Asset;
use UserBundle\Entity\User;

/**
 * Game
 *
 * @ORM\Table(name="games")
 * @ORM\Entity(repositoryClass="MatchBundle\Repository\GameRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Game
{
    // Status
    const STATUS_WAIT = 0;
    const STATUS_RUN  = 1;
    const STATUS_END  = 2;

    // Number of player => size of grid
    private $sizeList = [
        0 => 50, // Default
        2 => 15,
        3 => 20,
        4 => 25,
        5 => 25,
        6 => 30,
        7 => 30,
        8 => 35,
        9 => 40,
        10 => 45,
    ];

    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=128)
     * @Asset\Length(
     *     min="4",
     *     max="128",
     *     minMessage="The game's name length must be greather than {{ limit }} characters",
     *     maxMessage="The game's name length must be less than {{ limit }} characters")
     */
    protected $name;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, length=200)
     * @Gedmo\Slug(fields={"name"})
     */
    protected $slug;

    /**
     * @var integer
     * @ORM\Column(type="smallint", length=1, options={"unsigned"=true})
     */
    protected $status;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="UserBundle\Entity\User", fetch="EXTRA_LAZY")
     */
    protected $creator;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $runAt;

    /**
     * @var \DateTime
     * @ORM\Column(name="last", type="datetime", nullable=true)
     */
    protected $lastShoot;

    /**
     * @var int
     * @ORM\Column(type="smallint", options={"unsigned"=true})
     * @Asset\Range(
     *     min="15",
     *     max="50",
     *     minMessage="The grid size must be greater then {{ limit }}x{{ limit }}",
     *     maxMessage="The grid size must be under {{ limit }}x{{ limit }}")
     */
    protected $size;

    /**
     * @var array
     * @ORM\Column(type="simple_array", nullable=true)
     */
    protected $tour;

    /**
     * @var integer
     * @ORM\Column(type="smallint", length=1, options={"unsigned"=true})
     * @Asset\Range(
     *     min="2",
     *     max="12",
     *     minMessage="The number of player must be greater then {{ limit }}",
     *     maxMessage="The number of player must be under {{ limit }}")
     */
    protected $maxPlayer;

    /**
     * @var array
     * @ORM\Column(type="json_array")
     */
    protected $options;

    /**
     * @var array
     * @ORM\Column(type="array", nullable=true)
     */
    protected $grid;

    /**
     * @var ArrayCollection|Player[]
     * @ORM\OneToMany(targetEntity="MatchBundle\Entity\Player", mappedBy="game", cascade={"remove"})
     * @ORM\OrderBy({"position": "ASC"})
     */
    protected $players;

    /**
     * Game constructor.
     */
    public function __construct()
    {
        $this->status = self::STATUS_WAIT;
        $this->players = new ArrayCollection();
        $this->createAt = new \DateTime();
        $this->maxPlayer = 4;
        $this->tour = [];
        $this->options = [];
        $this->grid = [];
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
     * Get Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set name
     * @param string $name
     * @return Game
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get Status
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set status
     * @param int $status
     * @return Game
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get Slug
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Convert to string
     * @return string
     */
    public function __toString()
    {
        return $this->slug;
    }

    /**
     * Get Creator
     * @return User
     */
    public function getCreator()
    {
        return $this->creator;
    }

    /**
     * Is the creator ?
     * @param User $user User to test
     * @return bool
     */
    public function isCreator(User $user = null)
    {
        return $user !== null && $this->creator->getId() === $user->getId();
    }

    /**
     * Set creator
     * @param User $creator
     * @return Game
     */
    public function setCreator(User $creator)
    {
        $this->creator = $creator;

        return $this;
    }

    /**
     * Get CreateAt
     * @return \DateTime
     */
    public function getCreateAt()
    {
        return $this->createAt;
    }

    /**
     * Get RunAt
     * @return \DateTime
     */
    public function getRunAt()
    {
        return $this->runAt;
    }

    /**
     * Set runAt
     * @param \DateTime $runAt
     * @return Game
     */
    public function setRunAt(\DateTime $runAt)
    {
        $this->runAt = $runAt;

        return $this;
    }

    /**
     * Get LastShoot
     * @return \DateTime
     */
    public function getLastShoot()
    {
        return $this->lastShoot;
    }

    /**
     * Set lastShoot
     * @param \DateTime|null $lastShoot The last shoot or null for now
     * @return Game
     */
    public function setLastShoot(\DateTime $lastShoot = null)
    {
        if (!$lastShoot) {
            $lastShoot = new \DateTime('now');
        }
        $this->lastShoot = $lastShoot;

        return $this;
    }

    /**
     * Get Size
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Set size
     * @param int $size
     * @return Game
     */
    public function setSize($size)
    {
        $size = intval($size);
        $this->size = min($this->sizeList[0], max($this->sizeList[2], $size));

        return $this;
    }

    /**
     * Get grid size from max players
     * @return integer
     */
    public function getSizeFromPlayersNb()
    {
        if (array_key_exists($this->maxPlayer, $this->sizeList)) {
            return $this->sizeList[$this->maxPlayer];
        }

        return $this->sizeList[0];
    }

    /**
     * Set grid size from the max player
     * @ORM\PrePersist()
     * @return Game
     */
    public function setSizeFromPlayers()
    {
        $this->size = $this->getSizeFromPlayersNb();

        return $this;
    }


    /**
     * Get Tour
     * @return array
     */
    public function getTour()
    {
        return $this->tour;
    }

    /**
     * Set tour
     * @param array $tour
     * @return Game
     */
    public function setTour(array $tour)
    {
        $this->tour = $tour;

        return $this;
    }

    /**
     * Get MaxPlayer
     * @return int
     */
    public function getMaxPlayer()
    {
        return $this->maxPlayer;
    }

    /**
     * Set maxPlayer
     * @param integer $maxPlayer
     * @return Game
     */
    public function setMaxPlayer($maxPlayer)
    {
        $maxPlayer = intval($maxPlayer);
        $this->maxPlayer = min(12, max(2, $maxPlayer));

        return $this;
    }

    /**
     * Get Options
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Get a option value
     * @param string $name    The name of option
     * @param mixed  $default Default value
     *
     * @return mixed
     */
    public function getOption($name, $default)
    {
        return (array_key_exists($name, $this->options)) ? $this->options[$name] : $default;
    }

    /**
     * Set options
     * @param array $options
     * @return Game
     */
    public function setOptions(array $options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Set one option
     * @param string $name
     * @param mixed  $value
     *
     * @return Game
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }

    /**
     * Remove a option
     * @param string $name
     *
     * @return Game
     */
    public function removeOption($name)
    {
        if (array_key_exists($name, $this->options)) {
            unset($this->options[$name]);
        }

        return $this;
    }

    /**
     * Get Grid
     * @return array
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * Set grid
     * @param array $grid
     * @return Game
     */
    public function setGrid(array $grid)
    {
        $this->grid = $grid;

        return $this;
    }

    /**
     * Get Players
     * @return ArrayCollection|Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * Set players
     * @param ArrayCollection|Player[] $players
     * @return Game
     */
    public function setPlayers($players)
    {
        $this->players = $players;

        return $this;
    }

    /**
     * Is player is in game ?
     * @param Player $player
     *
     * @return bool
     */
    public function hasPlayer(Player $player)
    {
        return $this->players->contains($player);
    }

    /**
     * Add a player
     * @param Player $player
     * @return Game
     */
    public function addPlayer(Player $player)
    {
        if (!$this->hasPlayer($player)) {
            $player->setGame($this);
            $this->players->add($player);
        }

        return $this;
    }

    /**
     * Remove a player
     * @param Player $player
     * @return Game
     */
    public function removePlayer(Player $player)
    {
        if ($this->hasPlayer($player)) {
            $this->players->removeElement($player);
        }

        return $this;
    }

    /**
     * Get a player by this position
     * @param integer $position
     *
     * @return Player|null
     */
    public function getPlayerByPosition($position)
    {
        foreach ($this->getPlayers() as $player) {
            if ($player->getPosition() == $position) {
                return $player;
            }
        }

        return null;
    }

    /**
     * Get players who had play
     * @return Player[]|array
     */
    public function getPlayersTour()
    {
        $tour = [];
        foreach ($this->getPlayers() as $player) {
            if (in_array($player->getPosition(), $this->tour)) {
                $tour[] = $player;
            }
        }

        return $tour;
    }

    /**
     * Get a box of the grid
     * @param integer $x X coord
     * @param integer $y Y coord
     *
     * @return Box
     */
    public function getBox($x, $y)
    {
        $box = new Box($x, $y);
        $box->populateFromGrid($this->getGrid());

        return $box;
    }

    /**
     * Convert to aray
     * @return array
     */
    public function toArray()
    {
        // Common informations
        $infos = [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'slug' => $this->slug,
            'size' => $this->size,
            'date' => $this->createAt->getTimestamp(),
            'options' => $this->options,
        ];

        // Informations depend status
        switch ($this->status) {
            case self::STATUS_WAIT:
                $infos = array_merge($infos, [
                    'nb' => $this->players->count(),
                    'max' => $this->maxPlayer,
                    'creatorName' => $this->creator->getUsername(),
                ]);
                break;

            case self::STATUS_RUN:
                $players = $this->getPlayersTour();
                $tour = [];
                foreach ($players as $player) {
                    $tour[] = [
                        'id' => $player->getUser()->getId(),
                        'name' => $player->getName(),
                        ];
                }
                $infos = array_merge($infos, [
                    'tour' => $tour,
                    'creatorName' => $this->creator->getUsername(),
                    'date' => $this->getRunAt()->getTimestamp(),
                    'last' => $this->getLastShoot()->getTimestamp(),
                ]);
                break;

            case self::STATUS_END:
                $players = $this->getPlayersTour();
                $tour = [];
                foreach ($players as $player) {
                    if ($player->getLife() > 0) {
                        $tour[] = $player->getName();
                    }
                }
                $infos = array_merge($infos, [
                    'tour' => $tour,
                    'date' => $this->getRunAt()->getTimestamp(),
                    'enddate' => $this->getLastShoot()->getTimestamp(),
                ]);
                break;
        }

        return $infos;
    }
}
