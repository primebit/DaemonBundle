<?php
namespace Hq\DaemonsBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(name="daemons.daemons_groups")
 */
class DaemonsGroup
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
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $created_at;

    /** 
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $updated_at;

    /**
     * @ORM\OneToMany(targetEntity="Daemon", mappedBy="group", cascade="remove")
     */
    private $daemons;

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
     * @return DaemonsGroup
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
     * @return DaemonsGroup
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
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return DaemonsGroup
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
     * @return DaemonsGroup
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
        $this->daemons = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Add daemons
     *
     * @param \Hq\DaemonsBundle\Entity\Daemon $daemons
     * @return DaemonsGroup
     */
    public function addDaemon(\Hq\DaemonsBundle\Entity\Daemon $daemons)
    {
        $this->daemons[] = $daemons;
    
        return $this;
    }

    /**
     * Remove daemons
     *
     * @param \Hq\DaemonsBundle\Entity\Daemon $daemons
     */
    public function removeDaemon(\Hq\DaemonsBundle\Entity\Daemon $daemons)
    {
        $this->daemons->removeElement($daemons);
    }

    /**
     * Get daemons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getDaemons()
    {
        return $this->daemons;
    }
}