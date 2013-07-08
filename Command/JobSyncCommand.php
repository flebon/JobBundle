<?php
namespace Tessi\JobBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

//Entities
use Tessi\JobBundle\Entity\Job;

class JobSyncCommand extends ContainerAwareCommand
{
	const MAX_TASK_PROCESS = 1;

    protected function configure()
    {
        $this
            ->setName('job:sync')
            ->setDescription('Synchronize running tasks')
            ->addOption(
               'debug',
               null,
               InputOption::VALUE_NONE,
               'Debug mode'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	$em    = $this->getContainer()->get('doctrine')->getEntityManager('job');
		$conn  = $this->getContainer()->get('doctrine.dbal.job_connection');
		$limit = self::MAX_TASK_PROCESS;
		$time  = date('1970-01-01 H:i:s');
    	$sql   = "
			UPDATE `jobbundlejob` j 
			SET j.currentRunningCount = 
				(	SELECT count(*) FROM jobbundletask t
					WHERE t.startdate > DATE_SUB(NOW(), INTERVAL j.taskTimeOut SECOND) 
					AND t.job_id = j.id
					AND t.enddate IS NULL );
    	";
        $conn->query($sql);
    }
}

?>
