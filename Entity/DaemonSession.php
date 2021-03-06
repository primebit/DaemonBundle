<?php
namespace Hq\DaemonsBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(name="daemons.daemons_sessions")
 * @ORM\Entity(repositoryClass="Hq\DaemonsBundle\EntityRepository\DaemonSessionRepository")
 */
class DaemonSession
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="bigint")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $started_at;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finished_at;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Daemon", inversedBy="sessions")
     */
    private $daemon;

    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $status;

    /**
     * @ORM\OneToMany(targetEntity="DaemonMessage", mappedBy="session", cascade="remove")
     */
    private $messages;

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
     * Set started_at
     *
     * @param \DateTime $startedAt
     * @return DaemonSession
     */
    public function setStartedAt($startedAt)
    {
        $this->started_at = $startedAt;
    
        return $this;
    }

    /**
     * Get started_at
     *
     * @return \DateTime 
     */
    public function getStartedAt()
    {
        return $this->started_at;
    }

    /**
     * Set finished_at
     *
     * @param \DateTime $finishedAt
     * @return DaemonSession
     */
    public function setFinishedAt($finishedAt)
    {
        $this->finished_at = $finishedAt;
    
        return $this;
    }

    /**
     * Get finished_at
     *
     * @return \DateTime 
     */
    public function getFinishedAt()
    {
        return $this->finished_at;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set daemon
     *
     * @param \Hq\DaemonsBundle\Entity\Daemon $daemon
     * @return DaemonSession
     */
    public function setDaemon(\Hq\DaemonsBundle\Entity\Daemon $daemon = null)
    {
        $this->daemon = $daemon;
    
        return $this;
    }

    /**
     * Get daemon
     *
     * @return \Hq\DaemonsBundle\Entity\Daemon 
     */
    public function getDaemon()
    {
        return $this->daemon;
    }

    /**
     * Add messages
     *
     * @param \Hq\DaemonsBundle\Entity\DaemonMessage $messages
     * @return DaemonSession
     */
    public function addMessage(\Hq\DaemonsBundle\Entity\DaemonMessage $messages)
    {
        $this->messages[] = $messages;
    
        return $this;
    }

    /**
     * Remove messages
     *
     * @param \Hq\DaemonsBundle\Entity\DaemonMessage $messages
     */
    public function removeMessage(\Hq\DaemonsBundle\Entity\DaemonMessage $messages)
    {
        $this->messages->removeElement($messages);
    }

    /**
     * Get messages
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set status
     *
     * @param integer $status
     * @return DaemonSession
     */
    public function setStatus($status)
    {
        $this->status = $status;
    
        return $this;
    }

    /**
     * Get status
     *
     * @return integer 
     */
    public function getStatus()
    {
        return $this->status;
    }
}