<?php

namespace Tessi\JobBundle\Job;

use Symfony\Component\DependencyInjection\ContainerAware;

abstract class Job extends ContainerAware
{
	public function __construct($container)
	{
		$this->setContainer($container);
	}

	public function createTasks()
	{
		
	}

	public function executeTask($input)
	{
		
	}
}