<?php
namespace Hq\DaemonsBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(name="daemons.daemons_messages")
 */
class DaemonMessage
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="text", nullable=false)
     */
    private $text;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DaemonSession", inversedBy="messages")
     */
    private $session;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $type;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return DaemonMessage
     */
    public function setText($text)
    {
        $this->text = $text;
    
        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return DaemonMessage
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;
    
        return $this;
    }

    /**
     * Get created_at
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Set session
     *
     * @param \Hq\DaemonsBundle\Entity\DaemonSession $session
     * @return DaemonMessage
     */
    public function setSession(\Hq\DaemonsBundle\Entity\DaemonSession $session = null)
    {
        $this->session = $session;
    
        return $this;
    }

    /**
     * Get session
     *
     * @return \Hq\DaemonsBundle\Entity\DaemonSession 
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set type
     *
     * @param integer $type
     * @return DaemonMessage
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }
}