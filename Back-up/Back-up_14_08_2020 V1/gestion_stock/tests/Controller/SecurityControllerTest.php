<?php
namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{

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