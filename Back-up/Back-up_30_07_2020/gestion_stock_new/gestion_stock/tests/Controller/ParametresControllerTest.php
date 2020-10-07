<?php
namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ParametresControllerTest extends WebTestCase
{
    public function testIndexNonLog(){
        $client = static::createClient();
        $client->request('GET','/parametre');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }
    public function testIndex(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testIndexSiMauvaisLogin(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('user');

        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre');
        $this->assertEquals(403,$client->getResponse()->getStatusCode());
    }
    public function testAddMarque(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/add/Marque');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testAddType(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/add/Type');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testAddSpecificite(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/add/Specificite');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testAddLieu(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/add/Lieu');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testAddInconnus(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/add/Test');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }
    public function testEditMarque(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/edit/Marque/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testEditType(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/edit/Type/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testEditSpecificite(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/edit/Specificite/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testEditLieu(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/edit/Lieu/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testEditInconnus(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/edit/Test/1');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }
    public function testDeleteMarque(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/delete/Marque/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDeleteType(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/delete/Type/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDeleteSpecificite(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/delete/Specificite/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDeleteLieu(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/delete/Lieu/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDeleteInconnus(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/parametre/delete/Test/1');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }
}