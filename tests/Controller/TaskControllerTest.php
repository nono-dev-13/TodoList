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

class PageControllerTest extends WebTestCase
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

    public function testIndexPage() 
    {
        //$client = $this->createClient();
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testh4IndexPage()
    {
        //$client = $this->createClient();
        $this->client->request('GET', '/');
        $this->assertSelectorTextContains('h4', 'Tâche à faire');
    }

    public function testAdminPageRestricted()
    {
        //$client = $this->createClient();
        $this->client->request('GET', '/admin');
        $this->assertResponseRedirects('/login');
    }

    public function testAdminPageAutorized()
    {
        //$client = $this->createClient();

        $this->databaseTool->loadFixtures([AppTestFixtures::class]);

        $userAdmin = $this->getUserAdmin();
        $this->client->loginUser($userAdmin);
        $this->client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h2', 'Liste des utilisateurs');
    }

    public function testMenuAdmin()
    {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        // je me log
        $this->client->loginUser($this->getUserAdmin());
        
        $this->client->request('GET', '/');
        $this->assertSelectorTextContains('.navbar-nav', 'Gestion utilisateur');
    }

    public function testMenuUser()
    {
        //$client = $this->createClient();
        $user = $this->getUserRoleUser();
        $this->client->loginUser($user);
        $this->client->request('GET', '/');
        $this->assertSelectorTextNotContains('.navbar-nav', 'Gestion utilisateur');
    }

    public function testCreateTask()
    {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log
        $this->client->loginUser($this->getUserAdmin());
        $crawler = $this->client->request('GET', '/task/create');
        $form = $crawler->selectButton('Ajouter une tâche')->form([
            'task[title]' => 'première tâche de test',
            'task[content]' => 'contenu tâche de test'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this-> assertSelectorExists('.alert.alert-success');

    }

    public function testCreateTaskNotUser()
    {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log
        $crawler = $this->client->request('GET', '/task/create');
        
        $this->assertResponseRedirects('/login');

    }

    public function testDeleteTaskAdmin()
    {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log
        $this->client->loginUser($this->getUserAdmin());
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "titre de la tache 0"])->getId();
        
        $this->client->request('GET', '/task/delete/'.$id);
        
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this-> assertSelectorExists('.alert.alert-success');

    }

    public function testDeleteTaskUser()
    {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log
        $this->client->loginUser($this->getUserRoleUser());
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "titre de la tache 1"])->getId();
        
        $this->client->request('GET', '/task/delete/'.$id);
        
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this-> assertSelectorExists('.alert.alert-danger');

    }

    public function testDeleteTaskWithoutUser()
    {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => "titre de la tache 0"])->getId();
        
        $this->client->request('GET', '/task/delete/'.$id);
        
        $this->assertResponseRedirects('/login');

    }

    public function testToogleTaskUser()
    {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log
        $this->client->loginUser($this->getUserRoleUser());
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['isDone' => 0])->getId();
        
        $this->client->request('GET', '/task/toogle/'.$id);
        
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this-> assertSelectorExists('.alert.alert-success');

    }

    public function testToogleTaskAdmin()
    {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log
        $this->client->loginUser($this->getUserAdmin());
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['isDone' => 0])->getId();
        
        $this->client->request('GET', '/task/toogle/'.$id);
        
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this-> assertSelectorExists('.alert.alert-success');

    }

    public function testToogleTaskNotUser()
    {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log pas
        
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['isDone' => 0])->getId();
        
        $this->client->request('GET', '/task/toogle/'.$id);
        
        $this->assertResponseRedirects('/login');
    }

    public function testToogleTaskNotOwned()
    {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log
        $this->client->loginUser($this->getUserRoleUser());
        $admin = $this->getUserAdmin();
 
        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['isDone' => 0, 'user' => $admin])->getId();
        
        $this->client->request('GET', '/task/toogle/'.$id);
        
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this-> assertSelectorExists('.alert.alert-danger');

    }

    public function testTaskDone()
    {
        $this->client->request('GET', '/task-done');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEditTaskNotUser() {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log pas

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => 'titre de la tache 1'])->getId();
        
        $this->client->request('GET', '/task/edit/'.$id);
        
        $this->assertResponseRedirects('/login');
    }

    public function testEditTaskUser() {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log

        $this->client->loginUser($this->getUserRoleUser());

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => 'titre de la tache 0'])->getId();
        
        $crawler = $this->client->request('GET', '/task/edit/'.$id);
        $form = $crawler->selectButton('Modifier la tâche')->form([
            'task[title]' => 'Modification de tâche de test',
            'task[content]' => 'Modification de tâche de test'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this-> assertSelectorExists('.alert.alert-success');
    }

    public function testEditTaskUserNotOwned() {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user
        // je me log

        $this->client->loginUser($this->getUserRoleUser());

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => 'titre de la tache 3'])->getId();
        
        $crawler = $this->client->request('GET', '/task/edit/'.$id);
        $form = $crawler->selectButton('Modifier la tâche')->form([
            'task[title]' => 'Modification de tâche de test',
            'task[content]' => 'Modification de tâche de test'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/?id='.$id);
        $this->client->followRedirect();
        $this-> assertSelectorExists('.alert.alert-danger');
    }

    public function testEditTaskAdmin() {
        $this->databaseTool->loadFixtures([AppTestFixtures::class]);
        //log user ou admin
        // je me log

        $this->client->loginUser($this->getUserAdmin());

        $taskRepository = static::getContainer()->get(TaskRepository::class);
        $id = $taskRepository->findOneBy(['title' => 'titre de la tache 1'])->getId();
        
        $crawler = $this->client->request('GET', '/task/edit/'.$id);
        $form = $crawler->selectButton('Modifier la tâche')->form([
            'task[title]' => 'Modification de tâche de test',
            'task[content]' => 'Modification de tâche de test'
        ]);
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
        $this->client->followRedirect();
        $this-> assertSelectorExists('.alert.alert-success');
    }
}