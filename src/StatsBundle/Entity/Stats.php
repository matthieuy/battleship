<?php

namespace StatsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stats
 *
 * @ORM\Table(name="stats")
 * @ORM\Entity(repositoryClass="StatsBundle\Repository\StatsRepository")
 */
class Stats
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer|null
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $gameId;

    /**
     * @var integer|null
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $userId;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $stat;

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    protected $value;

    /**
     * Get id
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get GameId
     * @return integer|null
     */
    public function getGameId()
    {
        return $this->gameId;
    }

    /**
     * Set GameId
     * @param integer|null $gameId
     *
     * @return $this
     */
    public function setGameId($gameId)
    {
        $this->gameId = $gameId;

        return $this;
    }

    /**
     * Get UserId
     * @return integer|null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set UserId
     * @param integer|null $userId
     *
     * @return $this
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get Stat
     * @return integer
     */
    public function getStat()
    {
        return $this->stat;
    }

    /**
     * Set Stat
     * @param integer $stat
     *
     * @return $this
     */
    public function setStat($stat)
    {
        $this->stat = $stat;

        return $this;
    }

    /**
     * Get Value
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set Value
     * @param integer $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
