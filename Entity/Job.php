<?php

namespace Tessi\JobBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Job
 *
 * @ORM\Table(name="jobbundlejob")
 * @ORM\Entity
 */
class Job
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=255)
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="namespace", type="string", length=255)
     */
    private $namespace;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startRangeDate", type="datetime")
     */
    private $startRangeDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endRangeDate", type="datetime")
     */
    private $endRangeDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    /**
     * @var integer
     *
     * @ORM\Column(name="maxConcurrentTasks", type="integer")
     */
    private $maxConcurrentTasks;

    /**
     * @var integer
     *
     * @ORM\Column(name="taskTimeOut", type="integer")
     */
    private $taskTimeOut;

    /**
     * @var integer
     *
     * @ORM\Column(name="startTaskRestrictionDate", type="datetime", nullable=true)
     */
    private $startTaskRestrictionDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="endTaskRestrictionDate", type="datetime", nullable=true)
     */
    private $endTaskRestrictionDate;


    public function __construct()
    {
        $this->setMaxConcurrentTasks(1000);
        $this->setTaskTimeOut(3600);
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
     * Set code
     *
     * @param string $code
     * @return Job
     */
    public function setCode($code)
    {
        $this->code = $code;
    
        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set namespace
     *
     * @param string $namespace
     * @return Job
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    
        return $this;
    }

    /**
     * Get namespace
     *
     * @return string 
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Set startRangeDate
     *
     * @param \DateTime $startRangeDate
     * @return Job
     */
    public function setStartRangeDate($startRangeDate)
    {
        $this->startRangeDate = $startRangeDate;
    
        return $this;
    }

    /**
     * Get startRangeDate
     *
     * @return \DateTime 
     */
    public function getStartRangeDate()
    {
        return $this->startRangeDate;
    }

    /**
     * Set endRangeDate
     *
     * @param \DateTime $endRangeDate
     * @return Job
     */
    public function setEndRangeDate($endRangeDate)
    {
        $this->endRangeDate = $endRangeDate;
    
        return $this;
    }

    /**
     * Get endRangeDate
     *
     * @return \DateTime 
     */
    public function getEndRangeDate()
    {
        return $this->endRangeDate;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     * @return Job
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    
        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean 
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set maxConcurrentTasks
     *
     * @param integer $maxConcurrentTasks
     * @return Job
     */
    public function setMaxConcurrentTasks($maxConcurrentTasks)
    {
        $this->maxConcurrentTasks = $maxConcurrentTasks;
    
        return $this;
    }

    /**
     * Get maxConcurrentTasks
     *
     * @return integer 
     */
    public function getMaxConcurrentTasks()
    {
        return $this->maxConcurrentTasks;
    }

    /**
     * Set taskTimeOut
     *
     * @param integer $taskTimeOut
     * @return Job
     */
    public function setTaskTimeOut($taskTimeOut)
    {
        $this->taskTimeOut = $taskTimeOut;
    
        return $this;
    }

    /**
     * Get taskTimeOut
     *
     * @return integer 
     */
    public function getTaskTimeOut()
    {
        return $this->taskTimeOut;
    }

    /**
     * Set startTaskRestrictionDate
     *
     * @param integer $startTaskRestrictionDate
     * @return Job
     */
    public function setStartTaskRestrictionDate($startTaskRestrictionDate)
    {
        $this->startTaskRestrictionDate = $startTaskRestrictionDate;
    
        return $this;
    }

    /**
     * Get startTaskRestrictionDate
     *
     * @return integer 
     */
    public function getStartTaskRestrictionDate()
    {
        return $this->startTaskRestrictionDate;
    }


    /**
     * Set startTaskRestrictionDate
     *
     * @param integer $endTaskRestrictionDate
     * @return Job
     */
    public function setEndTaskRestrictionDate($endTaskRestrictionDate)
    {
        $this->endTaskRestrictionDate = $endTaskRestrictionDate;
    
        return $this;
    }

    /**
     * Get endTaskRestrictionDate
     *
     * @return integer 
     */
    public function getEndTaskRestrictionDate()
    {
        return $this->endTaskRestrictionDate;
    }
}