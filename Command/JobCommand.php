<?php
namespace Tessi\JobBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//Entities
use Tessi\JobBundle\Entity\Job;

class JobCommand extends ContainerAwareCommand
{
	const MAX_TASK_PROCESS = 1;

    protected function configure()
    {
        $this
            ->setName('job:run')
            ->setDescription('Executes tasks and tasks creation. Input Point of Job Bundle CRON.')
            ->addOption(
               'debug',
               null,
               InputOption::VALUE_NONE,
               'Debug mode'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	set_time_limit(0);
		ini_set("memory_limit", -1);
		
    	$this->findAndExecuteTasks($input, $output);
    	//$this->createJobTasks($input, $output);
    }

    protected function findAndExecuteTasks(&$input, &$output)
    {
		$em   = $this->getContainer()->get('doctrine')->getEntityManager('job');
		$conn = $this->getContainer()->get('doctrine.dbal.job_connection');
		$time = date('1970-01-01 H:i:s');
    	$sql  = "
			SELECT t.id FROM jobbundletask t
			INNER JOIN jobbundlejob j ON j.id = t.job_id
			WHERE t.startdate is null AND t.enddate IS NULL
			AND  (
					SELECT count(*) FROM jobbundletask rt 
					WHERE rt.job_id = t.job_id 
					AND (rt.startdate IS NOT NULL AND rt.startdate >= DATE_SUB(NOW(),INTERVAL j.taskTimeOut SECOND))
					AND rt.enddate IS NULL
				  ) < j.maxconcurrenttasks

			AND j.startTaskRestrictionDate < '$time'
			AND j.endTaskRestrictionDate   > '$time'

			ORDER BY t.executiondate ASC
			LIMIT 1
    	";

        $ids = $conn->fetchAll($sql);

    	if(count($ids) == 0) {
			$output->writeln('----NO TASKS TO EXECUTE');
			return;
		}

    	//Lock table to prevent task doudle execution
		//$em->getConnection()->exec('LOCK TABLES _jobbundle_task AS _0_ READ;');

    	//On cherche les taches à executer
    	$foundTasks = $em->getRepository('JobBundle:Task')->createQueryBuilder('t')
							->andWhere('t.id IN (:ids)')
              				->setParameter('ids', $ids)
							->getQuery()->getResult();

		$taskToExecute = array();

		$startDate = new \DateTime('now');


    	//Lock task for other executions
		foreach($foundTasks as $task) {
			//$allRunningSisterTasks = $em->getRepository('JobBundle:Task')->getAllActiveSisterTasks($task->getJob());

			//If task is executable (concurrency)
			//if(count($allRunningSisterTasks) < $task->getJob()->getMaxConcurrentTasks()) {
				$taskToExecute[] = $task;
				$task->setStartDate($startDate);
				$em->persist($task);

				//On n'en prends qu'une
				if(count($taskToExecute) >= self::MAX_TASK_PROCESS)
					break;
			//}
		}

		$em->flush();

		//Unlock table
		//$em->getConnection()->exec('UNLOCK TABLES;');

		$output->writeln('-------------------------------------------');
		$output->writeln('----EXECUTION STARTING ON ' . count($taskToExecute) . ' TASK(S) [' . $startDate->format('Y-m-d H:i:s') . ']');
		
		foreach($taskToExecute as $task) {

			try {
				$output->writeln('--------Execution of task #' . $task->getId());
				$this->executeTache($task, $output);
			} catch(\Exception $e) {
				
				//throw $e;

				$output->writeln('--------ERROR (catched Exception) ON TASK #' . $task->getId());
				$task->setErrorMessage(	$e->getMessage() . chr(10) 
										. ' IN ' .      $e->getFile() . chr(10) 
										. ' ON LINE ' . $e->getLine() . chr(10) 
										. ' STACKSTRACE : ' .chr(10)
										. $e->getTraceAsString());
			}

			$task->setEndDate(new \DateTime('now'));
			$em->persist($task);
			$em->flush();
		}
		
		$endDate = new \DateTime('now');
		
		$output->writeln('----END OF EXECUTION OF ' . count($taskToExecute) . ' TASK(S) [' . $endDate->format('Y-m-d H:i:s') . ']');
	
    }

	protected function createJobTasks(&$input, &$output)
	{
		$em = $this->getContainer()->get('doctrine')->getEntityManager('job');
    	
    	$currentTime = new \DateTime(date('1970-01-01 H:i:s'));
    	
		//On cherche les taches à executer
		$jobsToExecute = $em->getRepository('JobBundle:Job')->createQueryBuilder('j')
							->andWhere('j.startRangeDate < :currentDate')
							->andWhere('j.endRangeDate > :currentDate')
							->andWhere('j.isActive = 1')
							->setParameter('currentDate', $currentTime)
							//->orderBy('j.executionDate', 'ASC')
							//->setMaxResults(1)
							->getQuery()->getResult();

    	foreach($jobsToExecute as $job) {
			try {
				$this->executeCreateJobTask($job, $output);
			} catch(\Exception $e) {
				$output->writeln('--------ERROR (catched Exception) ON JOB #' . $job->getId());
			}
    	}
	}

	protected function executeCreateJobTask($job, &$output = null)
	{

		$scriptNamespace = $job->getNamespace();

		if(!class_exists($scriptNamespace)) {
			$output->writeln('--------SCRIPT $scriptNamespace class do not exists.');
			throw new \Exception("$scriptNamespace class do not exists.");
		}
		
		$output->writeln('--------TASKS CREATION ON JOB ' . $job->getCode() . ' DATE : ' . date('Y-m-d H:i:s'));
		
		$script = new $scriptNamespace($this->getContainer());
		$script->createTasks();
	}

	protected function executeTache($task, &$output = null)
	{
		$scriptNamespace = $task->getJob()->getNamespace();

		if(!class_exists($scriptNamespace)) {
			$output->writeln('--------SCRIPT $scriptNamespace class do not exists.');
			throw new \Exception("$scriptNamespace class do not exists.");
		}
		
		$output->writeln('--------SCRIPT ' . $task->getJob()->getNamespace() . ' ON TASK #' . $task->getId());
		
		$script = new $scriptNamespace($this->getContainer());
		$input  =  (array) json_decode($task->getInput());
		$script->executeTask($input);
	}
}

?>