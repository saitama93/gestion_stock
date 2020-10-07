<?php
namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HistoriqueControllerTest extends WebTestCase
{
    public function testIndexNonLog(){
        $client = static::createClient();
        $client->request('GET','/historique');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }
    public function testIndex(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/historique');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDetails(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/historique/details/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
}