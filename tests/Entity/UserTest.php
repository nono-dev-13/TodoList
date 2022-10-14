<?php
namespace App\Tests\User;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\ConstraintViolation;

use function PHPUnit\Framework\assertEquals;

class UserTest extends KernelTestCase {

    public function testValidEntityUser () {
        $user = new User();
        $user->setEmail('user@user.fr')
            ->setRoles(["ROLE_ADMIN"])
            ->setPassword('password')
            ->setUsername('Arnaud');

        self::bootKernel();
        $container = static::getContainer();
        $error = $container->get('validator')->validate($user);
        $this->assertCount(0, $error);

        $this->assertEquals($user->getEmail(), 'user@user.fr');
        $this->assertEquals($user->getUserIdentifier(), 'user@user.fr');
        $this->assertEquals($user->getRoles(), '["ROLE_ADMIN"]');
        $this->assertEquals($user->getPassword(), 'password');
        $this->assertEquals($user->getUsername(),'Arnaud');
    
    }
}