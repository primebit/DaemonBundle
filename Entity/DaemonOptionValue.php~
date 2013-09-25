<?php
namespace Hq\DaemonsBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(name="daemons.daemons_options_values")
 */
class DaemonOptionValue
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /** 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $value;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $created_at;

    /** 
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updated_at;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="DaemonOption")
     */
    private $option;

    /**
     * Set value
     *
     * @param string $value
     * @return DaemonOptionValue
     */
    public function setValue($value)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return string 
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set created_at
     *
     * @param \DateTime $createdAt
     * @return DaemonOptionValue
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
     * @return DaemonOptionValue
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set option
     *
     * @param \Hq\DaemonsBundle\Entity\DaemonOption $option
     * @return DaemonOptionValue
     */
    public function setOption(\Hq\DaemonsBundle\Entity\DaemonOption $option = null)
    {
        $this->option = $option;
    
        return $this;
    }

    /**
     * Get option
     *
     * @return \Hq\DaemonsBundle\Entity\DaemonOption 
     */
    public function getOption()
    {
        return $this->option;
    }
}