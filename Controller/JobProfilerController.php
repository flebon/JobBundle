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

//Filtered Lists
use Tessi\JobBundle\FilterList\RunningTasks;
use Tessi\JobBundle\FilterList\JobsList;

class JobProfilerController extends Controller
{
	
    /**
     * @Route("/admin/offres_liste", name="Admin_offres_liste")
     * @Template()
     */
    public function listeAction()
    {
        $listeOffre = $this->get('filterlist')
                           ->setList(new OffreFilterList())
                           ->getClientList($this->generateUrl('Admin_offres_liste_ajax'));

        return array(
            'listeOffres' => $listeOffre
        );
    }



	/**
	 * @Route("/jobprofiler/main", name="JobBundle_main")
	 * @Template()
	 */
	public function mainAction()
	{
        $tasksList = $this->get('filterlist')
                   ->setList(new RunningTasks())
                   ->getClientList($this->generateUrl('JobBundle_main_tasks_list_ajax'));

    	return array(
            'tasksList' => $tasksList
        );
	}
    
    /**
     * @Route("/jobprofiler/tasks_list_ajax", name="JobBundle_main_tasks_list_ajax")
     * @Template()
     */
    public function listeAjaxAction()
    {
        return $this->get('filterlist')
                ->setList(new RunningTasks())
                ->bindAjaxRequest($this->get('request'));
    }

	/**
	 * @Route("/jobprofiler/task/{idTask}/details", name="JobBundle_task_details")
	 * @Template()
	 */
	public function taskDetailsAction($idTask)
	{
        $em   = $this->getDoctrine()->getEntityManager('job');
		$task = $em->getRepository('JobBundle:Task')->find($idTask);

		return array('task' => $task, 'input' => json_decode($task->getInput()));
	}

	/**
	 * @Route("/jobprofiler/parametrage_list", name="JobBundle_parametrage_liste")
	 * @Template()
	 */
	public function parametrageListeAction()
	{
        $jobList = $this->get('filterlist')
                   ->setList(new JobsList())
                   ->getClientList($this->generateUrl('JobBundle_job_list_ajax'));

        return array(
            'jobList' => $jobList
        );
	}

    /**
     * @Route("/jobprofiler/tasks_list_job_ajax", name="JobBundle_job_list_ajax")
     * @Template()
     */
    public function listeJobAjaxAction()
    {
        return $this->get('filterlist')
                ->setList(new JobsList())
                ->bindAjaxRequest($this->get('request'));
    }

	/**
	 * @Route("/jobprofiler/parametrage/{idJob}", name="JobBundle_parametrage_edit")
	 * @Template()
	 */
	public function parametrageEditAction($idJob)
	{
        $em = $this->getDoctrine()->getEntityManager('job');

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
            ->add('startRangeDate',     'time',     array('label' => "Range start (launch)"))
            ->add('endRangeDate',       'time',     array('label' => "Range end (launch)"))

            ->add('startTaskRestrictionDate', 'time', array('label' => "Task execution range"))
            ->add('endTaskRestrictionDate',   'time', array('label' => "Task execution range"))

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

                    if(method_exists($this->get('session'), 'setFlash'))
                        $this->get('session')->setFlash('success', "Opération effectuée");
                    else
                        $this->get('session')->getFlashBag()->set('success', "Opération effectuée");
                    
                    return $this->redirect($this->generateUrl('JobBundle_parametrage_edit', array('idJob' => $job->getId()))); 
                } catch(\Exception $e) {
                    if(method_exists($this->get('session'), 'setFlash'))
                        $this->get('session')->setFlash('error', "Problème pendant l'opération : " . $e->getMessage());
                    else
                        $this->get('session')->getFlashBag()->set('error', "Problème pendant l'opération : " . $e->getMessage());
                } 
            }
        }
    
        return array('job' => $job, 'form' => $form->createView());
	}

    /**
     * @Route("/jobprofiler/job/{idJob}/delete", name="JobBundle_job_delete")
     * @Template()
     */
    public function jobDeleteAction($idJob)
    {
        $em   = $this->getDoctrine()->getEntityManager('job');
        $job  = $em->getRepository('JobBundle:Job')->find($idJob);
        
        $em->remove($job);
        $em->flush();

        return $this->redirect($this->generateUrl('JobBundle_parametrage_liste'));
    }
}
