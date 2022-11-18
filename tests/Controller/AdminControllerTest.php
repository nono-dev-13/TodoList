<?php

namespace App\Tests\Controller;

use App\DataFixtures\AppTestFixtures;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class AdminControllerTest extends WebTestCase
{
    /**
     * @var AbstractDatabaseTool
     */
    protected $databaseTool;

    private KernelBrowser $client;

    public function setUp(): void
    {
        parent::setUp();
        
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        self::ensureKernelShutdown();
        $this->client = static::createClient();
    }

    public function getUserAdmin()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneByEmail('admin@admin.fr');
    }

    public function getUserRoleUser()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneByEmail('user@user.fr');
    }

    public function testEditUser() {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log

        $this->client->loginUser($this->getUserAdmin());

        $taskRepository = static::getContainer()->get(UserRepository::class);
        $id = $taskRepository->findOneBy(['username' => 'Arnaud'])->getId();
        
        $crawler = $this->client->request('GET', '/admin/edit/'.$id);
        $form = $crawler->selectButton('Modifier un utilisateur')->form([
            'edit_user[email]' => 'admin2@admin.fr',
            'edit_user[roles]' => 'ROLE_ADMIN',
            'edit_user[username]' => 'Nouveau Arnaud'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin');
        $this->client->followRedirect();
    }

    public function testCreateUser() {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log

        $this->client->loginUser($this->getUserAdmin());       
        
        $crawler = $this->client->request('GET', '/admin');
        $form = $crawler->selectButton('Ajouter un utilisateur')->form([
            'user[email]' => 'arnaud@arnaud.fr',
            'user[roles]' => 'ROLE_ADMIN',
            'user[password]' => 'password-2',
            'user[username]' => 'The Admin'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin');
        $this->client->followRedirect();
    }

    public function testDeleteUser() {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log

        $this->client->loginUser($this->getUserAdmin());       
        
        $taskRepository = static::getContainer()->get(UserRepository::class);
        $id = $taskRepository->findOneBy(['username' => "Arnaud"])->getId();
        
        $this->client->request('GET', '/admin/delete/'.$id);
        
        $this->assertResponseRedirects('/admin');
    }

    public function testModifyPasswordUser() {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log

        $this->client->loginUser($this->getUserAdmin());       
        $taskRepository = static::getContainer()->get(UserRepository::class);
        $id = $taskRepository->findOneBy(['username' => 'Arnaud'])->getId();
        $crawler = $this->client->request('GET', '/admin/change-pass/'.$id);
        $form = $crawler->selectButton('Modifier mot de passe')->form([
            'change_password[password]' => 'password-2',
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/admin');
        $this->client->followRedirect();
    }

    

    

    

    

    

    
}