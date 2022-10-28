<?php
namespace App\Tests\User;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

use function PHPUnit\Framework\assertEquals;

class UserTest extends KernelTestCase {

    /**
     * Get Task
     */
    public function getTask(): Task {
        $task = new Task();
        $task->setTitle('Title');
        $task->setContent('TO DO');

        return $task;
    }

    /**
     * Get User
     */
    public function getUser() {
        $user = new User();
        $user->setUsername('Username');
        $user->setEmail('user@user.fr');
        $user->setPassword('password');
        $user->addTask($this->getTask());

        return $user;
    }

    public function testValidEntityUser () {
        $user = new User();
        $user->setEmail('user@user.fr')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword('password')
            ->setUsername('Arnaud');

        $this->assertEquals($user->getEmail(), 'user@user.fr');
        $this->assertEquals($user->getUserIdentifier(), 'user@user.fr');
        $this->assertEquals($user->getRoles(), ["ROLE_ADMIN", "ROLE_USER"]);
        $this->assertEquals($user->getPassword(), 'password');
        $this->assertEquals($user->getUsername(),'Arnaud');

        self::bootKernel();
        $container = static::getContainer();
        $error = $container->get('validator')->validate($user);
        $this->assertCount(0, $error);
    
    }

    /**
     * Test add user tasks
     */
    public function testValidUserAddTasks() {
        $user = $this->getUser();
        $task = $this->getTask();

        $user->addTask($task);
        $this->assertNotEmpty($user->getTasks());

        $user->removeTask($user->getTasks()[0]);
        $user->removeTask($task);
        $this->assertEmpty($user->getTasks());

        self::bootKernel();
        $container = static::getContainer();
        $error = $container->get('validator')->validate($user);
        $this->assertCount(0, $error);
    }
}