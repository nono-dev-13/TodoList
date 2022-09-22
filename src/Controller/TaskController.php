<?php

namespace App\Controller;

use App\Repository\TaskRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(TaskRepository $taskRepository): Response
    {
        $listTask = $taskRepository->findAll();
        return $this->render('task/index.html.twig', [
            'listTask' => $listTask
        ]);
    }
}
