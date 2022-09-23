<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository, Request $request): Response
    {
        $listUser = $userRepository->findAll();
        
        $user = new User();
        $formAddUser = $this->createForm(UserType::class, $user);
        $formAddUser->handleRequest($request);
        
        return $this->render('admin/index.html.twig', [
            'listUser' => $listUser,
            'formAddUser' => $formAddUser->createView(),
        ]);
        
        
    }
}
