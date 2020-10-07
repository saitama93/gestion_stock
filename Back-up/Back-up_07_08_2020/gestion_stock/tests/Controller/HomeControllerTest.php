<?php
namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testIndex(){
        $client = static::createClient();
        $client->request('GET','/');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testGoBack(){
        $client = static::createClient();
        $client->request('GET','/goBack/Materiel/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testGoBackURLFausse(){
        $client = static::createClient();
        $client->request('GET','/goBack/Test/1');
        $this->assertEquals(500,$client->getResponse()->getStatusCode());
    }
}
