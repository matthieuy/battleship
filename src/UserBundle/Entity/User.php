<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class User
 *
 * @package UserBundle\Entity
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string Slug
     * @ORM\Column(name="slug", length=255, unique=true)
     * @Gedmo\Slug(fields={"username"}, updatable=true)
     */
    protected $slug;

    /**
     * @var boolean User is a AI ?
     * @ORM\Column(name="ai", type="boolean")
     */
    protected $ai;

    /**
     * User constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->ai = false;
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
     * Get slug
     * @return string Slug
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get Ai
     * @return boolean
     */
    public function isAi()
    {
        return $this->ai;
    }

    /**
     * Set ai
     * @param boolean $ai
     * @return User
     */
    public function setAi($ai)
    {
        $this->ai = $ai;

        return $this;
    }
}