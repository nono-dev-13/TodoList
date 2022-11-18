<?php

namespace App\DataFixtures;

// use DateTime;
// use Faker\Factory;
use App\Entity\Task;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @codeCoverageIgnore
 */
class AppTestFixtures extends Fixture
{
    private $userPasswordHasher;
    
    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    
    public function load(ObjectManager $manager): void
    {
        //$faker = Factory::create();
        
        $listUser = []; 
        
        //création de 1 user ROLE USER
        $user1 = new User;
        $user1->setEmail('user@user.fr');
        $user1->setPassword($this->userPasswordHasher->hashPassword($user1, "password"));
        $user1->setUsername('Chardane');
        $user1->setRoles(["ROLE_USER"]);
        $listUser[] = $user1;

        $manager->persist($user1);

        //création de 1 user ROLE ADMIN
        $user2 = new User;
        $user2->setEmail('admin@admin.fr');
        $user2->setPassword($this->userPasswordHasher->hashPassword($user2, "password"));
        $user2->setUsername('Arnaud');
        $user2->setRoles(["ROLE_ADMIN"]);
        $listUser[] = $user2;

        $manager->persist($user2);

        // Création d'une vingtaine de tache avec user
        for ($i = 0; $i < 20; $i++) {
            $task = new Task;
            $task->setTitle('titre de la tache '. $i);
            $task->setCreatedAt(new DateTimeImmutable('now'));
            $task->setContent('contenu de la tâche ' .$i);
            $task->setUser($listUser[$i % 2]);
            
            $manager->persist($task);
        }

        $manager->flush();
    }
}
