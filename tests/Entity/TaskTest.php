<?php
namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

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
    public function getTask(\DateTimeImmutable $dateTimeNow): Task {
        $task = new Task();
        $task->setTitle('Title');
        $task->setContent('TO DO');
        $task->setCreatedAt($dateTimeNow);
        $task->setUser($this->getUser());

        return $task;
    }
}