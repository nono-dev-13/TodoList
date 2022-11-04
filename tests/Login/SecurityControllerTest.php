<?php

namespace App\Tests\Login;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageSecurityTest extends WebTestCase
{
    public function testDisplayLogin() 
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('h1', 'Connectez-vous');
        $this->assertSelectorNotExists('.alert.alert-danger');
    }

    public function testLoginWithBadCredentials() 
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'nono@nono.fr',
            'password' => 'test'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/login');
        $client->followRedirect();
        $this-> assertSelectorExists('.alert.alert-danger');
    }

    
    public function testSuccessFullLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form([
            'email' => 'admin@admin.fr',
            'password' => 'password'
        ]);
        $client->submit($form);
        $this->assertResponseRedirects('/');
    }
}