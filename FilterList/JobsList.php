<?php

namespace Tessi\JobBundle\FilterList;


use VVG\Bundle\FilterListBundle\FilterList\FilterListBase;
use VVG\Bundle\FilterListBundle\Field\FilterListField;


class JobsList extends FilterListBase 
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
		$field->setName('code');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Code');
		$this->addField($field);

		$field = new FilterListField();
		$field->setName('script');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Script namespace');
		$this->addField($field);

		/*
		$field = new FilterListField();
		$field->setName('range_start');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Start range');
		$this->addField($field);

		$field = new FilterListField();
		$field->setName('range_start');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('End range');
		$this->addField($field);

		$field = new FilterListField();
		$field->setName('execution_start');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Job');
		$this->addField($field);

		$field = new FilterListField();
		$field->setName('execution_end');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Execution start');
		$this->addField($field);
		*/

		$field = new FilterListField();
		$field->setName('is_active');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Is active');
		$this->addField($field);

		$field = new FilterListField();
		$field->setName('concurrency');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Concurrency');
		$this->addField($field);

		$field = new FilterListField();
		$field->setName('timeout');
		$field->setIsFiltrable(true);
		$field->setIsSortable(true);
		$field->setDisplayName('Timeout');
		$this->addField($field);
	}
	
	public function configureRepository()
	{
		$repo = $this->getDoctrine()->getManager('job')->getRepository('JobBundle:Job');
		$this->setRepository($repo);
	}
	
	public function configureQuery()
	{
		$queryBuilder = $this->getRepository()->createQueryBuilder('j');
		$this->setQueryBuilder($queryBuilder);
	}
		
	public function configureFieldQuery($fieldName, $fieldValue)
	{	
		if($fieldName == 'id')
		{
			$this->getQueryBuilder()->andWhere("j.id = '". $fieldValue . "'");
		}

		if($fieldName == 'code')
		{
			$this->getQueryBuilder()->andWhere("j.code LIKE '%". $fieldValue ."%'");
		}

		if($fieldName == 'script')
		{
			$this->getQueryBuilder()->andWhere("j.script LIKE '%". $fieldValue ."%'");
		}

		if($fieldName == 'is_active')
		{
			if(strtolower($fieldValue) == 'yes')
				$this->getQueryBuilder()->andWhere("j.isActive = 1");

			if(strtolower($fieldValue) == 'no')
				$this->getQueryBuilder()->andWhere("j.isActive = 0");
		}

		if($fieldName == 'concurrency')
		{
			$this->getQueryBuilder()->andWhere("j.maxConcurrentTasks LIKE '%". $fieldValue ."%'");
		}

		if($fieldName == 'timeout')
		{
			$this->getQueryBuilder()->andWhere("j.taskTimeOut LIKE '%". $fieldValue ."%'");
		}
	}
	
	public function configureOrderByQuery($fieldName, $sort)
	{
		if($fieldName == 'id')
		{
			$this->getQueryBuilder()->orderBy("j.id", $sort);
		}

		if($fieldName == 'code')
		{
			$this->getQueryBuilder()->orderBy("j.code", $sort);
		}

		if($fieldName == 'script')
		{
			$this->getQueryBuilder()->orderBy("j.script", $sort);
		}

		if($fieldName == 'concurrency')
		{
			$this->getQueryBuilder()->orderBy("j.maxConcurrentTasks", $sort);
		}

		if($fieldName == 'timeout')
		{
			$this->getQueryBuilder()->orderBy("j.taskTimeOut", $sort);
		}
	}
	
	public function getFieldResult($fieldName, $result)
	{
		if($fieldName == 'id')
		{
			return $result->getId();
		}

		if($fieldName == 'code')
		{
			return $result->getCode();
		}
		
		if($fieldName == 'script')
		{
			return $result->getNamespace();
		}

		if($fieldName == 'is_active')
		{
			return $result->getIsActive() ? 'YES' : 'NO';
		}

		if($fieldName == 'concurrency')
		{
			return $result->getMaxConcurrentTasks();
		}

		if($fieldName == 'timeout')
		{
			return $result->getTaskTimeOut();
		}
	}
	
	public function getTotalCountResult()
	{
		return 'COUNT(j)';
	}
	
	public function hrefLink($result)
	{
		return $this->getRouter()->generate('JobBundle_parametrage_edit', array('idJob' => $result->getId()));
	}
	
	public function getPrimary($result)
	{
		return $result->getId();
	}
	
	public function getEvents()
	{
		return array(
			'onDelete' => 'Delete task',
		);
	}
	
	protected function onDelete($entity) {

		$this->getDoctrine()->getManager('job')->remove($entity);
		$this->getDoctrine()->getManager('job')->flush();
	}
}
