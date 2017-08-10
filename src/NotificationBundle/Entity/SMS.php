<?php

namespace NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * SMS
 *
 * @ORM\Table(name="notification_sms")
 * @ORM\Entity(repositoryClass="NotificationBundle\Repository\SMSRepository")
 */
class SMS
{
    /**
     * @var int
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=15)
     */
    protected $number;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    protected $message;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime")
     */
    protected $createDate;

    /**
     * SMS constructor.
     */
    public function __construct()
    {
        $this->createDate = new \DateTime();
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
     * Get Number
     * @return string
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set Number
     * @param string $number
     *
     * @return $this
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get Message
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set Message
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get CreateDate
     * @return \DateTime
     */
    public function getCreateDate()
    {
        return $this->createDate;
    }
}
