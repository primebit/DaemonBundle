<?php
namespace Hq\DaemonsBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(name="daemons.daemons")
 */
class Daemon
{
    /** 
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /** 
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $name;

    /** 
     * @ORM\Column(type="text", nullable=false)
     */
    private $description;

    /** 
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $is_active;

    /** 
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $is_realtime;

    /** 
     * @ORM\Column(type="integer", nullable=false)
     */
    private $max_instances;

    /** 
     * @ORM\Column(type="integer", nullable=false)
     */
    private $max_execution_time;

    /** 
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $send_php_messages;

    /** 
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $send_important_messages;

    /** 
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $send_final_log;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="DaemonsGroup", inversedBy="daemons")
     */
    private $group;

    /**
     * @ORM\OneToMany(targetEntity="DaemonOption", mappedBy="daemon", cascade="remove")
     */
    private $options;

    /**
     * @ORM\OneToMany(targetEntity="IncomeParser", mappedBy="daemon", cascade="remove")
     */
    private $income_parsers;

    /**
     * @ORM\OneToMany(targetEntity="DaemonSession", mappedBy="daemon", cascade="remove")
     */
    private $sessions;

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
     * Set name
     *
     * @param string $name
     * @return Daemon
     */
    public function setName($name)
    {
        $this->name = $name;
    
        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Daemon
     */
    public function setDescription($description)
    {
        $this->description = $description;
    
        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set is_active
     *
     * @param boolean $isActive
     * @return Daemon
     */
    public function setIsActive($isActive)
    {
        $this->is_active = $isActive;
    
        return $this;
    }

    /**
     * Get is_active
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Set is_realtime
     *
     * @param boolean $isRealtime
     * @return Daemon
     */
    public function setIsRealtime($isRealtime)
    {
        $this->is_realtime = $isRealtime;
    
        return $this;
    }

    /**
     * Get is_realtime
     *
     * @return boolean 
     */
    public function getIsRealtime()
    {
        return $this->is_realtime;
    }

    /**
     * Set max_instances
     *
     * @param integer $maxInstances
     * @return Daemon
     */
    public function setMaxInstances($maxInstances)
    {
        $this->max_instances = $maxInstances;
    
        return $this;
    }

    /**
     * Get max_instances
     *
     * @return integer 
     */
    public function getMaxInstances()
    {
        return $this->max_instances;
    }

    /**
     * Set max_execution_time
     *
     * @param integer $maxExecutionTime
     * @return Daemon
     */
    public function setMaxExecutionTime($maxExecutionTime)
    {
        $this->max_execution_time = $maxExecutionTime;
    
        return $this;
    }

    /**
     * Get max_execution_time
     *
     * @return integer 
     */
    public function getMaxExecutionTime()
    {
        return $this->max_execution_time;
    }

    /**
     * Set send_php_messages
     *
     * @param boolean $sendPhpMessages
     * @return Daemon
     */
    public function setSendPhpMessages($sendPhpMessages)
    {
        $this->send_php_messages = $sendPhpMessages;
    
        return $this;
    }

    /**
     * Get send_php_messages
     *
     * @return boolean 
     */
    public function getSendPhpMessages()
    {
        return $this->send_php_messages;
    }

    /**
     * Set send_important_messages
     *
     * @param boolean $sendImportantMessages
     * @return Daemon
     */
    public function setSendImportantMessages($sendImportantMessages)
    {
        $this->send_important_messages = $sendImportantMessages;
    
        return $this;
    }

    /**
     * Get send_important_messages
     *
     * @return boolean 
     */
    public function getSendImportantMessages()
    {
        return $this->send_important_messages;
    }

    /**
     * Set send_final_log
     *
     * @param boolean $sendFinalLog
     * @return Daemon
     */
    public function setSendFinalLog($sendFinalLog)
    {
        $this->send_final_log = $sendFinalLog;
    
        return $this;
    }

    /**
     * Get send_final_log
     *
     * @return boolean 
     */
    public function getSendFinalLog()
    {
        return $this->send_final_log;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return Daemon
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
     * Set updated_at
     *
     * @param \DateTime $updatedAt
     * @return Daemon
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updated_at = $updatedAt;
    
        return $this;
    }

    /**
     * Get updated_at
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->options = new \Doctrine\Common\Collections\ArrayCollection();
        $this->income_parsers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set group
     *
     * @param \Hq\DaemonsBundle\Entity\DaemonsGroup $group
     * @return Daemon
     */
    public function setGroup(\Hq\DaemonsBundle\Entity\DaemonsGroup $group = null)
    {
        $this->group = $group;
    
        return $this;
    }

    /**
     * Get group
     *
     * @return \Hq\DaemonsBundle\Entity\DaemonsGroup 
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add options
     *
     * @param \Hq\DaemonsBundle\Entity\DaemonOption $options
     * @return Daemon
     */
    public function addOption(\Hq\DaemonsBundle\Entity\DaemonOption $options)
    {
        $this->options[] = $options;
    
        return $this;
    }

    /**
     * Remove options
     *
     * @param \Hq\DaemonsBundle\Entity\DaemonOption $options
     */
    public function removeOption(\Hq\DaemonsBundle\Entity\DaemonOption $options)
    {
        $this->options->removeElement($options);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Add income_parsers
     *
     * @param \Hq\DaemonsBundle\Entity\IncomeParser $incomeParsers
     * @return Daemon
     */
    public function addIncomeParser(\Hq\DaemonsBundle\Entity\IncomeParser $incomeParsers)
    {
        $this->income_parsers[] = $incomeParsers;
    
        return $this;
    }

    /**
     * Remove income_parsers
     *
     * @param \Hq\DaemonsBundle\Entity\IncomeParser $incomeParsers
     */
    public function removeIncomeParser(\Hq\DaemonsBundle\Entity\IncomeParser $incomeParsers)
    {
        $this->income_parsers->removeElement($incomeParsers);
    }

    /**
     * Get income_parsers
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIncomeParsers()
    {
        return $this->income_parsers;
    }

    /**
     * Add sessions
     *
     * @param \Hq\DaemonsBundle\Entity\DaemonSession $sessions
     * @return Daemon
     */
    public function addSession(\Hq\DaemonsBundle\Entity\DaemonSession $sessions)
    {
        $this->sessions[] = $sessions;
    
        return $this;
    }

    /**
     * Remove sessions
     *
     * @param \Hq\DaemonsBundle\Entity\DaemonSession $sessions
     */
    public function removeSession(\Hq\DaemonsBundle\Entity\DaemonSession $sessions)
    {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSessions()
    {
        return $this->sessions;
    }
}