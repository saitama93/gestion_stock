<?php
namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

    public function testLogin(){
        $client = static::createClient();
        $client->request('GET','/login');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }

    public function testLoginEtantAuthentifier(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/login');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }

    public function testLoginTentantDeSAuthentifier(){
        $client = static::createClient();

        $client->request('GET','/login');
        $crawler = $client->request('GET','/login');
        $crawler = $client->submitForm('Se connecter',['username'=>'root','password'=>'rootroot1234'],'POST');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }

    public function testLoginAvecMauvaisLogin(){
        $client = static::createClient();

        $client->request('GET','/login');
        $crawler = $client->request('GET','/login');
        $crawler = $client->submitForm('Se connecter',['username'=>'test','password'=>'x'],'POST');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }

    public function testLoginAvecMauvaisMdp(){
        $client = static::createClient();

        $client->request('GET','/login');
        $crawler = $client->request('GET','/login');
        $crawler = $client->submitForm('Se connecter',['username'=>'root','password'=>'jambonbeurre'],'POST');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }

    public function testLogout(){
        $client = static::createClient();
        $client->request('GET','/logout');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }

    public function testLogoutEtantAuthentifier(){
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);
        $client->request('GET','/logout');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }
}