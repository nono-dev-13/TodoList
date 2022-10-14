<?php
namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

use function PHPUnit\Framework\assertEquals;

class TaskTest extends KernelTestCase {

    public function createUser () {
        $user = new User();
        $user->setEmail('user@user.fr')
        ->setUsername('Arnaud');

        return $user;
    }

    public function testValidEntityTask () {
        $task = new Task();
        $date = new \DateTimeImmutable();
        $user = $this->createUser();
        $task->setTitle('Un titre')
            ->setContent('Un contenu')
            ->setCreatedAt($date)
            ->setIsDone(true)
            ->setUser($user);

        self::bootKernel();
        $container = static::getContainer();
        $error = $container->get('validator')->validate($task);
        $this->assertCount(0, $error);

        $this->assertEquals($task->getTitle(), 'Un titre');
        $this->assertEquals($task->getContent(), 'Un contenu');
        $this->assertEquals($task->getCreatedAt(), $date);
        $this->assertEquals($task->isIsDone(), true);
        $this->assertEquals($task->getUser(),$user);
    
    }

    public function testToogle() {
        $task = new Task();
        $date = new \DateTimeImmutable();
        $user = $this->createUser();
        $task->setTitle('Un titre')
            ->setContent('Un contenu')
            ->setCreatedAt($date)
            ->setIsDone(true)
            ->setUser($user);
        $task->toggle(false);
        $this->assertEquals($task->isIsDone(), false);
    }
    
    public function testInvalidMinEntity () {
        $task = new Task();
        $task->setTitle('u')
            ->setContent('')
            ->setCreatedAt(new \DateTimeImmutable());

        self::bootKernel();
        $container = static::getContainer();
        $error = $container->get('validator')->validate($task);
        $this->assertCount(2, $error);
    }
    
    public function testInvalidMaxEntity () {
        $task = new Task();
        $task->setTitle('zaeaeazeaze aze azeaz eaz eaz ea ze aze azeazeaze azeaze azeaze aze aze az eazeazeazeaze azeaz eazeazeazeaze azeaze azeazeazeazeazeaze azeazeazeazeazezaeazeazeazeazezaze za eza eaze aze aze az eaz e z eaz e azeazeazeeazeazeazezaeazeaze za eaz eaz e za eaz e az e az ea ze az e az e aze a zeaze az e az e az ea ze az e az e az e az e az eaze')
            ->setContent('')
            ->setCreatedAt(new \DateTimeImmutable());

        self::bootKernel();
        $container = static::getContainer();
        $error = $container->get('validator')->validate($task);
        $this->assertCount(2, $error);
    }
}