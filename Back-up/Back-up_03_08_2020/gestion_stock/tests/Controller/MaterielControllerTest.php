<?php
namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MaterielControllerTest extends WebTestCase
{

    public function testIndexNonLog(){
        $client = static::createClient();
        $client->request('GET','/stock');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }

    public function testIndexSiMauvaisLogin(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('user');

        $client->loginUser($testUser[0]);

        $client->request('GET','/stock');
        $this->assertEquals(403,$client->getResponse()->getStatusCode());
    }

    public function testIndex(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/stock');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }

    public function testIndexAvecRechercheNomMateriel(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);
        $client->followRedirects();
        $crawler = $client->request('GET','/stock');
        $form = $crawler->selectButton('search_button')->form();
        $form['search_num'] = 'test';
        $client->submit($form);
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }


    public function testAdd(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/stock/add');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }

    public function testEdit(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/stock/edit/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDelete(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/stock/delete/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDownloadCsv(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/stock/csv');
        $this->assertEquals(500,$client->getResponse()->getStatusCode());
    }
    public function testCleanBase(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/stock/cleanBase');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testImportCsv(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/stock/importCSV');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
}