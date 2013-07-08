<?php

namespace Tessi\JobBundle\FilterList;


use VVG\Bundle\FilterListBundle\FilterList\FilterListBase;
use VVG\Bundle\FilterListBundle\Field\FilterListField;


class TasksHistory extends RunningTasks 
{
	public function configureQuery()
	{
		$queryBuilder = $this->getRepository()->createQueryBuilder('t');
		$queryBuilder->innerjoin('t.job', 'j');
		$queryBuilder->orderBy('t.executionDate', 'DESC');
		
		$this->setQueryBuilder($queryBuilder);
	}

}
