<?php

namespace Tessi\JobBundle\Services;

class TaskBuilder
{
	protected $_doctrine;

    public function __construct($doctrine)
    {
    	$this->_doctrine = $doctrine;
    }

    public function getDoctrine()
    {
        return $this->_doctrine;
    }

    /**
     * Create a task
     */
    public function createTask($jobCode, \DateTime $executionDate, $input = array())
    {
        $em = $this->getDoctrine()->getEntityManager();
        
        $job = $em->getRepository('JobBundle:Job')->findOneBy(array('code' => $jobCode));
        if(!$job)
            throw new \Exception("[TaskBuilder] Unable to find job : $jobCode");

        $task = new \Tessi\JobBundle\Entity\Task();
        $task->setJob($job);
        $task->setInput(json_encode($input));
        $task->setExecutionDate($executionDate);

        $em->persist($task);
        $em->flush();
    }
}
