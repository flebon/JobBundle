<?php
namespace Tessi\JobBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\SecurityContext;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

//Entités
use Tessi\JobBundle\Entity\Job;


class JobProfilerController extends Controller
{
	
	/**
	 * @Route("/jobprofiler/main", name="JobBundle_main")
	 * @Template()
	 */
	public function mainAction()
	{
    	$em = $this->getDoctrine()->getEntityManager();

    	$startDate = new \DateTime('-1 hours');
    	$endDate   = new \DateTime('+1 hours');

		$currentTasks = $em->getRepository('JobBundle:Task')->createQueryBuilder('t')
						   ->andWhere('(t.executionDate > :startDate AND t.executionDate < :endDate) OR t.endDate IS NULL OR t.endDate > :startDate')
						   ->orderBy('t.executionDate', 'DESC')
						   ->setParameter('startDate', $startDate)
						   ->setParameter('endDate', $endDate)
						   ->getQuery()->getResult();
    	
    	$tasks = array();

    	foreach($currentTasks as $currentTask) {
    		$task = array();

    		$task['id']            = $currentTask->getId();
    		$task['job']           = $currentTask->getJob()->getCode();
    		$task['executionDate'] = $currentTask->getExecutionDate()->format('Y-m-d H:i:s');
    		$task['startDate']     = ($currentTask->getStartDate()) ? $currentTask->getStartDate()->format('Y-m-d H:i:s') : null;
    		$task['endDate']       = ($currentTask->getEndDate()) ? $currentTask->getEndDate()->format('Y-m-d H:i:s') : null;
    		$task['status']        = $currentTask->getStatus();
            $task['isTimedOut']    = $currentTask->isTimedOut(new \DateTime('now'));

    		$tasks[] = $task;
    	}

    	return array('tasks' => $tasks);
	}

	/**
	 * @Route("/jobprofiler/task/{idTask}/details", name="JobBundle_task_details")
	 * @Template()
	 */
	public function taskDetailsAction($idTask)
	{
        $em   = $this->getDoctrine()->getEntityManager();
		$task = $em->getRepository('JobBundle:Task')->find($idTask);

		return array('task' => $task, 'input' => json_decode($task->getInput()));
	}

    /**
     * @Route("/jobprofiler/task/{idTask}/delete", name="JobBundle_task_delete")
     * @Template()
     */
    public function taskDeleteAction($idTask)
    {
        $em   = $this->getDoctrine()->getEntityManager();
        $task = $em->getRepository('JobBundle:Task')->find($idTask);
        
        $em->remove($task);
        $em->flush();

        return $this->redirect($this->generateUrl('JobBundle_main'));
    }
    
    /**
     * @Route("/jobprofiler/task/{idTask}/reset", name="JobBundle_task_reset")
     * @Template()
     */
    public function taskResetAction($idTask)
    {
        $em   = $this->getDoctrine()->getEntityManager();
        $task = $em->getRepository('JobBundle:Task')->find($idTask);
        
        $task->setStartDate(null);
        $task->setEndDate(null);
        $task->setErrorMessage(null);
        
        $em->persist($task);
        $em->flush();

        return $this->redirect($this->generateUrl('JobBundle_main'));
    }

	/**
	 * @Route("/jobprofiler/parametrage_list", name="JobBundle_parametrage_liste")
	 * @Template()
	 */
	public function parametrageListeAction()
	{
    	$em   = $this->getDoctrine()->getEntityManager();
		$jobs = $em->getRepository('JobBundle:Job')->findAll();
    	
    	return array('jobs' => $jobs);
	}

	/**
	 * @Route("/jobprofiler/parametrage/{idJob}", name="JobBundle_parametrage_edit")
	 * @Template()
	 */
	public function parametrageEditAction($idJob)
	{
        $em = $this->getDoctrine()->getEntityManager();

        if($idJob == 'new') {
            $job = new Job();
        } else {
            $job = $em->getRepository('JobBundle:Job')->find($idJob);
            if(!$job)
                throw new \Exception("Loterie #$id introuvable");
        }

        $form = $this->createFormBuilder($job)
            ->add('code',               'text',     array('label' => "Code"))
            ->add('namespace',          'text',     array('label' => "Script (namespace)"))
            ->add('startRangeDate',     'time',     array('label' => "Range start"))
            ->add('endRangeDate',       'time',     array('label' => "Range end"))
            ->add('isActive',           'checkbox', array('label' => "Active ?", 'required' => false))
            ->add('maxConcurrentTasks', 'text',     array('label' => "Max concurrent tasks"))
            ->add('taskTimeOut',        'text',     array('label' => "Single task timeout"))
            ->getForm();

        $request = $this->getRequest();

        if(method_exists($request,'isMethod')) {
            $isPost = $request->isMethod('POST');
        } else {
            $isPost = $request->getMethod() == 'POST';
        }

        if ($isPost) {
            if(method_exists($form,'bindRequest'))
                $form->bindRequest($request);
            else
                $form->bind($request);

            if ($form->isValid()) {
                try {
                    $em->persist($job);
                    $em->flush();

                    $this->get('session')->setFlash('success', "Opération effectuée");
                    return $this->redirect($this->generateUrl('JobBundle_parametrage_edit', array('idJob' => $job->getId()))); 
                } catch(\Exception $e) {
                    $this->get('session')->setFlash('error', "Problème pendant l'opération : " . $e->getMessage());
                } 
            }
        }
    
        return array('job' => $job, 'form' => $form->createView());
	}
}