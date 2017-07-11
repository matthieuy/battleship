<?php

namespace UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Asset;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class User
 *
 * @package UserBundle\Entity
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="UserBundle\Repository\UserRepository")
 */
class User extends BaseUser
{
    const OPTIONS_NAME = ['boxSize', 'displayGrid'];
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
     * @var UploadedFile|null
     * @Asset\Image(maxWidth="500", maxHeight="500")
     */
    protected $avatar;

    /**
     * @var array
     * @ORM\Column(type="json_array", nullable=true)
     */
    protected $options;

    /**
     * User constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->ai = false;
        $this->options = [];
    }

    /**
     * Get option
     * @param string $name
     *
     * @return mixed|null
     */
    public function __get($name)
    {
        if (in_array($name, self::OPTIONS_NAME)) {
            return $this->getOption($name, null);
        }

        return null;
    }

    /**
     * Set option
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function __set($name, $value)
    {
        if (in_array($name, self::OPTIONS_NAME)) {
            return $this->setOption($name, $value);
        }

        return $this;
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

    /**
     * Get avatar
     * @return null|UploadedFile
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set avatar
     * @param UploadedFile|null $avatar
     *
     * @return $this
     */
    public function setAvatar(UploadedFile $avatar = null)
    {
        $this->avatar = $avatar;

        return $this;
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
     * Get option value
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOption($name, $default)
    {
        return (array_key_exists($name, $this->options)) ? $this->options[$name] : $default;
    }

    /**
     * Set option value
     * @param string $name
     * @param mixed  $value
     *
     * @return $this
     */
    public function setOption($name, $value)
    {
        $this->options[$name] = $value;

        return $this;
    }
}
