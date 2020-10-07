<?php
namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserControllerTest extends WebTestCase
{
    public function testIndexNonLog(){
        $client = static::createClient();
        $client->request('GET','/user');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }

    public function testIndexSiMauvaisLogin(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('user');

        $client->loginUser($testUser[0]);

        $client->request('GET','/user');
        $this->assertEquals(403,$client->getResponse()->getStatusCode());
    }

    public function testIndex(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/user');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testAdd(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/user/add');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testEdit(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/user/edit/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDelete(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/user/delete/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
}