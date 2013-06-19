<?php

namespace Tessi\JobBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TaskRepository extends EntityRepository
{
	public function getAllActiveSisterTasks($taskJob)
	{
		$taskTimeOut = $taskJob->getTaskTimeOut();
		$dateTimeOut = new \DateTime("-$taskTimeOut seconds");

		//var_dump($dateTimeOut);
		//die;

		$qb = $this->_em->createQueryBuilder();
	    $qb->select('t')
	        ->from('Tessi\JobBundle\Entity\Task', 't')
	        ->where('t.job = :taskJob')
	        ->andWhere('t.startDate IS NOT NULL')
	        ->andWhere('t.startDate > :dateTimeOut')
	        ->andWhere('t.endDate IS NULL')
			->setParameter('taskJob',     $taskJob)
			->setParameter('dateTimeOut', $dateTimeOut);
	 
	    return $qb->getQuery()->getResult();
	}

	public function hasBeenExecutedToday($jobName, $input = array())
	{
		$dateToday    = new \DateTime("today");
		$dateTomorrow = new \DateTime("tomorrow");
		$job          = $this->_em->getRepository('JobBundle:Job')->findOneBy(array('code' => $jobName));

		$qb = $this->_em->createQueryBuilder();
	    $qb->select('t')
	        ->from('Tessi\JobBundle\Entity\Task', 't')
	        ->where('t.job = :job')
	        ->andWhere('t.executionDate >= :dateToday')
	        ->andWhere('t.executionDate <  :dateTomorrow')
	        ->andWhere('t.input = :input')
			->setParameter('job',      $job)
			->setParameter('dateToday',    $dateToday)
			->setParameter('dateTomorrow', $dateTomorrow)
			->setParameter('input', json_encode($input));

	    return count($qb->getQuery()->getResult()) > 0;
	}
}
