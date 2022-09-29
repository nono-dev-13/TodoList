<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\EditUserType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager): Response
    {
        $listUser = $userRepository->findAll();
        
        $user = new User();
        $formAddUser = $this->createForm(UserType::class, $user);
        $formAddUser->handleRequest($request);

        if($formAddUser->isSubmitted() and $formAddUser->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $formAddUser->get('password')->getData()
                    )
            );

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_admin');
        }
        
        return $this->render('admin/index.html.twig', [
            'listUser' => $listUser,
            'formAddUser' => $formAddUser->createView(),
        ]);
        
    }

    #[Route('/admin/edit/{id}', name: 'app_admin_edit')]
    public function edit(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager, User $user): Response
    {
        
        $formEditUser = $this->createForm(EditUserType::class, $user);
        $formEditUser->handleRequest($request);

        if($formEditUser->isSubmitted() and $formEditUser->isValid()) {

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_admin');
        }
        
        return $this->render('admin/edit.html.twig', [
            'user'=> $user,
            'formEditUser' => $formEditUser->createView(),
        ]);
        
    }

    #[Route('/admin/change-pass/{id}', name: 'app_admin_change_pass')]
    public function changePass(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $manager, User $user): Response
    {
        
        $formChangePassUser = $this->createForm(ChangePasswordType::class, $user);
        $formChangePassUser->handleRequest($request);

        if($formChangePassUser->isSubmitted() and $formChangePassUser->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                        $user,
                        $formChangePassUser->get('password')->getData()
                    )
            );

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('app_admin');
        }
        
        return $this->render('admin/change-pass.html.twig', [
            'user' => $user,
            'formChangePassUser' => $formChangePassUser->createView(),
        ]);
        
    }

    #[Route("/admin/delete/{id}", name: "app_admin_delete")]
    public function delete(User $user, EntityManagerInterface $manager, UserRepository $userRepository)
    {
        $user = $userRepository->find($user->getId());
        $manager->remove($user);
        $manager->flush();

        return $this->redirectToRoute('app_admin');
    }
}
