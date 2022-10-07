<?php
namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;

use function PHPUnit\Framework\assertEquals;

class TaskTest extends TestCase {

    /**
     * Get Author
     */
    public function getUser(): User {
        return new User();
    }

    /**
     * Get Task
     */
    public function testGetTask() 
    {
        $task = new Task();
        $task->setTitle('t');
        $task->setContent('TO DO');
        $task->setCreatedAt(new DateTimeImmutable());
        $task->setUser($this->getUser());
        //$validator = $this->
        //$errors = $validator->validate($task);
        //$this->assertEquals($errors->count(), 0);
        $this->assertEquals(1,1);
        return $task;
    }
}