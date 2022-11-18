<?php 

namespace App\Tests\Repository;


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class UserRepositoryTest extends KernelTestCase
{

    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;
    
    
    public function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
        
    }

    public function testSearchByUsername()
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => 'Arnaud'])
        ;

        $this->assertSame('Arnaud', $user->getUsername());
    }
}