<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
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
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testh4IndexPage()
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertSelectorTextContains('h4', 'Tâche à faire');
    }

    public function testAdminPageRestricted()
    {
        $client = static::createClient();
        $client->request('GET', '/admin');
        $this->assertResponseRedirects('/login');
    }

    public function testAdminPageAutorized()
    {
        $client = static::createClient();
        $userAdmin = $this->getUserAdmin();
        $client->loginUser($userAdmin);
        $client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h2', 'Liste des utilisateurs');
    }

    public function testMenuAdmin()
    {
        $client = static::createClient();
        $userAdmin = $this->getUserAdmin();
        $client->loginUser($userAdmin);
        $client->request('GET', '/');
        $this->assertSelectorTextContains('.navbar-nav', 'Gestion utilisateur');
    }

    public function testMenuUser()
    {
        $client = static::createClient();
        $user = $this->getUserRoleUser();
        $client->loginUser($user);
        $client->request('GET', '/');
        $this->assertSelectorTextNotContains('.navbar-nav', 'Gestion utilisateur');
    }
}