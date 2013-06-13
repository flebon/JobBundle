<?php

namespace Tessi\JobBundle\Job;

use Tessi\JobBundle\Job\Job as BaseJob;

use Symfony\Component\DependencyInjection\ContainerAware;

class TestJob extends BaseJob 
{
	public function createTasks()
	{
		$executionDate = new \DateTime('now');
		$this->container->get('job_task_builder')->createTask('PUSH_NOTIFICATION_MOBILE', $executionDate, array());
	}

	public function executeTask($input = null)
	{
		sleep(rand(20, 120));
	}
}