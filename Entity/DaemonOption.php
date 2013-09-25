<?php
namespace Hq\DaemonsBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(name="daemons.daemons_options")
 */
class DaemonOption
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
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $default_value;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Daemon", inversedBy="options")
     */
    private $daemon;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="DaemonOptionValue")
     */
    private $value;

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
     * @return DaemonOption
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
     * Set default_value
     *
     * @param string $defaultValue
     * @return DaemonOption
     */
    public function setDefaultValue($defaultValue)
    {
        $this->default_value = $defaultValue;
    
        return $this;
    }

    /**
     * Get default_value
     *
     * @return string 
     */
    public function getDefaultValue()
    {
        return $this->default_value;
    }

    /**
     * Set daemon
     *
     * @param \Hq\DaemonsBundle\Entity\Daemon $daemon
     * @return DaemonOption
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
     * Set value
     *
     * @param \Hq\DaemonsBundle\Entity\DaemonOptionValue $value
     * @return DaemonOption
     */
    public function setValue(\Hq\DaemonsBundle\Entity\DaemonOptionValue $value = null)
    {
        $this->value = $value;
    
        return $this;
    }

    /**
     * Get value
     *
     * @return \Hq\DaemonsBundle\Entity\DaemonOptionValue 
     */
    public function getValue()
    {
        return $this->value;
    }
}