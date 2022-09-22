<?php

namespace App\DataFixtures;

use DateTime;
use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();
        
        $listUser = []; 
        //création de 1 user ROLE USER
        $user1 = new User;
        $user1->setEmail($faker->email());
        $user1->setPassword($this->userPasswordHasher->hashPassword($user1, "password"));
        $user1->setUsername($faker->firstname());
        $user1->setRoles(["ROLE_USER"]);
        $listUser[] = $user1;

        $manager->persist($user1);
        
        //création de 2 user ROLE ADMIN
        
        for ($i = 0; $i < 2; $i++) {
            $user = new User;
            $user->setEmail($faker->email());
            $user->setPassword($this->userPasswordHasher->hashPassword($user, "password"));
            $user->setUsername($faker->firstname());
            $user->setRoles(["ROLE_ADMIN"]);
            $listUser[] = $user;
            
            $manager->persist($user); 
        }

        // Création d'une vingtaine de tache avec user
        for ($i = 0; $i < 20; $i++) {
            $task = new Task;
            $task->setTitle($faker->word());
            $task->setCreatedAt(new DateTimeImmutable('now'));
            $task->setContent($faker->paragraph());
            $task->setUser($listUser[array_rand($listUser)]);
            
            $manager->persist($task);
        }

        $manager->flush();
    }
}
