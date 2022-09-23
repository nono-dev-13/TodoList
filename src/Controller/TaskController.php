<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    /**
    * Affiche la home avec la liste des taches
    */
    #[Route('/', name: 'home')]
    public function index(TaskRepository $taskRepository, UserRepository $userRepository): Response
    {
        $listTask = $taskRepository->findAll();
        return $this->render('task/index.html.twig', [
            'listTask' => $listTask,
        ]);
    }

    /**
    * Affiche la page de création d'une tâche
    */
    #[Route('/task/create', name: 'task_create')]
    public function create(Request $request, EntityManagerInterface $manager, Task $task=null)
    {
        if ($this->getUser()) {
            $task = new Task();
            $formCreateTask = $this->createForm(TaskType::class, $task);
            $formCreateTask->handleRequest($request);
        
        
            if($formCreateTask->isSubmitted() and $formCreateTask->isValid()) {
                
                if(!$task->getId()) {
                    $task->setCreatedAt(new \DateTimeImmutable());
                }

                $task->setUser($this->getUser());
    
                $manager->persist($task);
                $manager->flush();
                
                $this->addFlash('success', 'Nouvelle tâche ajouté');
                return $this->redirectToRoute('home');
            }
    
            return $this->render('task/create.html.twig', [
                'formCreateTask'=>$formCreateTask->createView(),
                'editMode'=> $task->getId()!= null,
                'task'=> $task,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
        
    }

    /**
    * Affiche la page de modification d'une tâche
    */
    #[Route('/task/edit/{id}', name: 'task_edit')]
    public function edit(Request $request, EntityManagerInterface $manager, Task $task=null)
    {
        if ($this->getUser()) {
            
            if(!$task){
                $task = new Task();
            }
            
            $formEditTask = $this->createForm(TaskType::class, $task);
            $formEditTask->handleRequest($request);

            /**
            * récupère l'utilisateur connecté (via symfony) 
            * @var User
            */
            $connectedUser = $this->getUser();
    
            if($formEditTask->isSubmitted() and $formEditTask->isValid()) {
                if ($connectedUser->getId() == $task->getUser()->getId() || $connectedUser->getRoles(["ROLE_ADMIN"])){
                    
                    if(!$task->getId()) {
                        $task->setCreatedAt(new \DateTimeImmutable());
                    }
        
                    $manager->persist($task);
                    $manager->flush();
                    
                    $this->addFlash('success', 'Tâche Modifié');
                    return $this->redirectToRoute('home');
                } 
                else {
                    $this->addFlash('error', "Vous ne pouvez pas modifier cet tâche, vous n'êtes pas l'auteur");
                    return $this->redirectToRoute('home', ['id'=>$task->getId()]);
                }
                
            }
    
            return $this->render('task/edit.html.twig', [
                'formEditTask'=>$formEditTask->createView(),
                'task'=> $task,
            ]);
            
            } else {
                return $this->redirectToRoute('app_login');
            }
        
    }

    /**
    * Affiche la page de modification d'une tâche
    */
    #[Route('/task/delete/{id}', name: 'task_delete')]
    public function delete(Task $task, EntityManagerInterface $manager)
    {
        //dd($task->getUser()->getId());
        //dd($this->getUser());

        /**
         * @var User
         */
        
        if ($task->getUser()->getId() == $this->getUser()->getId() || $this->getUser()->getRoles(["ROLE_ADMIN"])) {
            $manager->remove($task);
            $manager->flush();
            $this->addFlash('success', 'La tâche a bien été supprimée.');
            return $this->redirectToRoute('home');   
        } else {
            $this->addFlash('error', "Vous n'êtes pas autorisé à supprimer cette tâche car vous n'êtes pas l'auteur");
            return $this->redirectToRoute('home');
        }
    }
}