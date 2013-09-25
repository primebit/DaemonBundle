<?php
namespace Hq\DaemonsBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/** 
 * @ORM\Entity
 * @ORM\Table(name="daemons.income_parsers")
 */
class IncomeParser
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    
    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $parse_period;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Hq\FinanceBundle\Entity\IncomeSource", inversedBy="parsers")
     */
    private $source;

    /**
     * @var integer
     *
     * @ORM\ManyToOne(targetEntity="Daemon", inversedBy="income_parsers")
     */
    private $daemon;

    /**
     * Set parse_period
     *
     * @param integer $parsePeriod
     * @return IncomeParser
     */
    public function setParsePeriod($parsePeriod)
    {
        $this->parse_period = $parsePeriod;
    
        return $this;
    }

    /**
     * Get parse_period
     *
     * @return integer 
     */
    public function getParsePeriod()
    {
        return $this->parse_period;
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
     * Set source
     *
     * @param \Hq\FinanceBundle\Entity\IncomeSource $source
     * @return IncomeParser
     */
    public function setSource(\Hq\FinanceBundle\Entity\IncomeSource $source = null)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return \Hq\FinanceBundle\Entity\IncomeSource 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set daemon
     *
     * @param \Hq\DaemonsBundle\Entity\Daemon $daemon
     * @return IncomeParser
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
}