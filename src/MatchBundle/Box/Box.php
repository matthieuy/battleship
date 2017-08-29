<?php

namespace MatchBundle\Box;

use MatchBundle\Entity\Player;
use MatchBundle\ImagesConstant;

/**
 * Class Box
 * @package MatchBundle\Entity
 */
class Box
{
    protected $x;
    protected $y;
    protected $img;
    protected $player;
    protected $team;
    protected $boat;
    protected $shoot;
    protected $dead = false;

    protected $score = [];
    protected $life = [];
    protected $isSink = false;
    protected $sinkInfo = [];

    /**
     * Box constructor.
     * @param integer $x
     * @param integer $y
     */
    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * Get X
     * @return integer
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * Get Y
     * @return integer
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * Get the owner position
     * @return integer
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Get boat number
     * @return null|integer
     */
    public function getBoat()
    {
        return $this->boat;
    }

    /**
     * Set score
     * @param Player $player
     *
     * @return $this
     */
    public function setScore(Player $player)
    {
        $this->score[$player->getPosition()] = $player->getScore();

        return $this;
    }

    /**
     * Set the image
     * @param integer $img Image number
     *
     * @return $this
     */
    public function setImg($img)
    {
        $this->img = $img;

        return $this;
    }

    /**
     * Get the image number
     * @return integer
     */
    public function getImg()
    {
        return $this->img;
    }

    /**
     * Set dead
     * @param boolean $dead
     *
     * @return $this
     */
    public function setDead($dead)
    {
        $this->dead = $dead;

        return $this;
    }

    /**
     * Add life to box
     * @param Player $player
     *
     * @return $this
     */
    public function setLife(Player $player)
    {
        $this->life[$player->getPosition()] = $player->getLife();

        return $this;
    }

    /**
     * Set boat is sink
     * @param bool $sink Sink
     *
     * @return $this
     */
    public function setSink($sink = true)
    {
        $this->isSink = $sink;

        return $this;
    }

    /**
     * Is sink
     * @return bool
     */
    public function isSink()
    {
        return $this->isSink;
    }

    /**
     * Add sink infos
     * @param array $infos
     *
     * @return $this
     */
    public function setSinkInfo(array $infos)
    {
        $this->sinkInfo[] = $infos;

        return $this;
    }

    /**
     * Set the shooter
     * @param Player $shooter
     *
     * @return $this
     */
    public function setShooter(Player $shooter = null)
    {
        $this->shoot = ($shooter === null) ? 0 : $shooter->getPosition();

        return $this;
    }

    /**
     * Populate box with grid info
     * @param array $grid The grid
     * @internal
     * @return $this
     */
    public function populateFromGrid(array $grid)
    {
        if (isset($grid[$this->y][$this->x])) {
            $box = $grid[$this->y][$this->x];
            foreach ($box as $k => $v) {
                if (property_exists($this, $k)) {
                    $this->$k = $v;
                }
            }
        }

        return $this;
    }

    /**
     * Already shoot
     * @return bool
     */
    public function isAlreadyShoot()
    {
        return ($this->shoot !== null);
    }

    /**
     * Is our boat
     * @param Player $player
     *
     * @return bool
     */
    public function isOwn(Player $player)
    {
        return ($this->player === $player->getPosition());
    }

    /**
     * Is the same team
     * @param Player $player
     *
     * @return bool
     */
    public function isSameTeam(Player $player = null)
    {
        return ($player && $this->team === $player->getTeam());
    }

    /**
     * Is box is empty
     * @return bool
     */
    public function isEmpty()
    {
        return ($this->img === null);
    }

    /**
     * Is out of the grid
     * @param integer $gridSize
     *
     * @return bool
     */
    public function isOffzone($gridSize)
    {
        return ($this->x < 0 || $this->y < 0 || $this->x >= $gridSize || $this->y >= $gridSize);
    }

    /**
     * Get info to insert into grid
     * @return array
     */
    public function getInfoToGrid()
    {
        $infos = [
            'img' => $this->img,
            'shoot' => $this->shoot,
        ];
        if ($this->player !== null) {
            $infos['player'] = $this->player;
        }
        if ($this->boat !== null) {
            $infos['boat'] = $this->boat;
        }
        if ($this->team !== null) {
            $infos['team'] = $this->team;
        }
        if ($this->dead) {
            $infos['dead'] = $this->dead;
        }
        if (!$this->dead && $this->shoot !== null && $this->img > 0) {
            $infos['explose'] = true;
        }

        return $infos;
    }

    /**
     * Get infos to return to players
     * @return array
     */
    public function getInfoToReturn()
    {
        $infos = [
            'x' => $this->x,
            'y' => $this->y,
            'img' => $this->img,
            'shoot' => $this->shoot,
        ];
        if (!$this->dead && $this->shoot !== null && $this->img > 0) {
            $infos['explose'] = true;
        }

        if ($this->player !== null) {
            $infos['player'] = $this->player;
            $infos['team'] = $this->team;
        }
        if ($this->dead) {
            $infos['dead'] = $this->dead;
        }
        if ($this->isSink) {
            $infos['sink'] = $this->sinkInfo;
        }
        if (!empty($this->life)) {
            $infos['life'] = $this->life;
        }
        if (!empty($this->score)) {
            $infos['score'] = $this->score;
        }

        return $infos;
    }
}
