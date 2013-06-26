<?php

namespace Tessi\JobBundle\FilterList;


use VVG\Bundle\FilterListBundle\FilterList\FilterListBase;
use VVG\Bundle\FilterListBundle\Field\FilterListField;


class RunningTasks extends FilterListBase 
{
	public function configureFields()
	{
		$field = new FilterListField();
		$field->setName('id');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('#');
		$this->addField($field);
		
		$field = new FilterListField();
		$field->setName('job');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Job');
		$this->addField($field);
		
		$field = new FilterListField();
		$field->setName('execution_date');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Execution date');
		$this->addField($field);

		$field = new FilterListField();
		$field->setName('start_date');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Start date');
		$this->addField($field);

		$field = new FilterListField();
		$field->setName('end_date');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('End date');
		$this->addField($field);

		$field = new FilterListField();
		$field->setName('status');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Status');
		$this->addField($field);
	}
	
	public function configureRepository()
	{
		$repo = $this->getDoctrine()->getManager()->getRepository('JobBundle:Task');
		$this->setRepository($repo);
	}
	
	public function configureQuery()
	{
		$startDate = new \DateTime('-1 hours');
    	$endDate   = new \DateTime('+1 hours');

		$queryBuilder = $this->getRepository()->createQueryBuilder('t');
		$queryBuilder->innerjoin('t.job', 'j');
		
		$queryBuilder->andWhere('(t.executionDate > :startDate AND t.executionDate < :endDate) OR t.endDate IS NULL OR t.endDate > :startDate');
		$queryBuilder->orderBy('t.executionDate', 'DESC');
		$queryBuilder->setParameter('startDate', $startDate);
		$queryBuilder->setParameter('endDate', $endDate);
		
		$this->setQueryBuilder($queryBuilder);
	}
		
	public function configureFieldQuery($fieldName, $fieldValue)
	{	
		if($fieldName == 'id')
		{
			$this->getQueryBuilder()->andWhere("t.id = '". $fieldValue . "'");
		}

		if($fieldName == 'job')
		{
			$this->getQueryBuilder()->andWhere("j.code LIKE '%". $fieldValue ."%'");
		}

		if($fieldName == 'execution_date')
		{
			$this->getQueryBuilder()->andWhere("t.executionDate LIKE '%". $fieldValue ."%'");
		}

		if($fieldName == 'start_date')
		{
			$this->getQueryBuilder()->andWhere("t.startDate LIKE '%". $fieldValue ."%'");
		}

		if($fieldName == 'end_date')
		{
			$this->getQueryBuilder()->andWhere("t.endDate LIKE '%". $fieldValue ."%'");
		}

		if($fieldName == 'status')
		{
			$now = new \DateTime('-5 seconds');
			switch($fieldValue) {
				case 'Error':
					$this->getQueryBuilder()->andWhere("t.errorMessage LIKE '%". $fieldValue ."%'");
					$this->getQueryBuilder()->andWhere("t.endDate IS NOT NULL");
				break;
				case 'Running':
					$this->getQueryBuilder()->andWhere("t.startDate IS NOT NULL");
					$this->getQueryBuilder()->andWhere("t.endDate > :now");
					$this->getQueryBuilder()->setParameter('now', $now);
				break;
				case 'Executed':
					$this->getQueryBuilder()->andWhere("t.startDate IS NOT NULL");
					$this->getQueryBuilder()->andWhere("t.endDate IS NOT NULL");
				break;
				case 'Pending':
					$this->getQueryBuilder()->andWhere("t.startDate IS NULL");
					$this->getQueryBuilder()->andWhere("t.endDate IS NULL");
				break;
			}
			//@TODO
			//$this->getQueryBuilder()->andWhere("t.EndDate LIKE '%". $fieldValue ."%'");
		}
	}
	
	public function configureOrderByQuery($fieldName, $sort)
	{
		if($fieldName == 'id')
		{
			$this->getQueryBuilder()->orderBy("t.id", $sort);
		}

		if($fieldName == 'job')
		{
			$this->getQueryBuilder()->orderBy("j.code", $sort);
		}

		if($fieldName == 'execution_date')
		{
			$this->getQueryBuilder()->orderBy("t.executionDate", $sort);
		}

		if($fieldName == 'start_date')
		{
			$this->getQueryBuilder()->orderBy("t.startDate", $sort);
		}

		if($fieldName == 'end_date')
		{
			$this->getQueryBuilder()->orderBy("t.endDate", $sort);
		}

		if($fieldName == 'status')
		{
			//@TODO
			//$this->getQueryBuilder()->andWhere("t.EndDate LIKE '%". $fieldValue ."%'");
		}
	}
	
	public function getFieldResult($fieldName, $result)
	{
		if($fieldName == 'id')
		{
			return $result->getId();
		}

		if($fieldName == 'job')
		{
			return $result->getJob()->getCode();
		}

		if($fieldName == 'execution_date')
		{
			if($result->getExecutionDate())
				return $result->getExecutionDate()->format('Y-m-d H:i:s');
		}

		if($fieldName == 'start_date')
		{
			if($result->getStartDate())
				return $result->getStartDate()->format('Y-m-d H:i:s');
		}

		if($fieldName == 'end_date')
		{
			if($result->getEndDate())
				return $result->getEndDate()->format('Y-m-d H:i:s');
		}

		if($fieldName == 'status')
		{
			
			switch($result->getStatus()) {
				case 'Error':
					$color = 'red';
				break;
				case 'Running':
					$color = 'green';
				break;
				case 'Executed':
					$color = 'blue';
				break;
				case 'Pending':
					$color = 'orange';
				break;
			}

			return '<b style="color:'.$color.'">' . $result->getStatus() . '</b>';
		}
	}
	
	public function getTotalCountResult()
	{
		return 'COUNT(t)';
	}
	
	public function hrefLink($result)
	{
		return $this->getRouter()->generate('JobBundle_task_details', array('idTask' => $result->getId()));
	}
	
	public function getPrimary($result)
	{
		return $result->getId();
	}
	
	public function getEvents()
	{
		return array(
			'onDelete' => 'Delete task',
			'onReset'  => 'Reset task'
		);
	}
	
	protected function onDelete($entity) {

		$this->getDoctrine()->getManager()->remove($entity);
		$this->getDoctrine()->getManager()->flush();
	}

	protected function onReset($entity) {

		$entity->setStartDate(null);
        $entity->setEndDate(null);
        $entity->setErrorMessage(null);
        
        $this->getDoctrine()->getManager()->persist($entity);
        $this->getDoctrine()->getManager()->flush();
	}
}
