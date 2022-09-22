<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManager;
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
        $task = new Task();
        $formCreateTask = $this->createForm(TaskType::class, $task);
        $formCreateTask->handleRequest($request);

        if($formCreateTask->isSubmitted() and $formCreateTask->isValid()) {
            
            if(!$task->getId()) {
                $task->setCreatedAt(new \DateTimeImmutable());
            }

            $manager->persist($task);
            $manager->flush();
            
            $this->addFlash('success', 'Nouvelle tâche ajouté');
            return $this->redirectToRoute('home');
        }

        return $this->render('task/create.html.twig', [
            'formCreateTask'=>$formCreateTask->createView(),
            'task'=> $task,
        ]);
    }
}