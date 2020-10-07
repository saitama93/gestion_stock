<?php
namespace App\Tests\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class InterventionControllerTest extends WebTestCase
{
    public function testIndexNonLog(){
        $client = static::createClient();
        $client->request('GET','/intervention');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }

    public function testIndexSiLoginUtilisateur(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('user');

        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }

    public function testIndex(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDepart(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/depart');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testAdd(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/add/1/0');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testListMateriel(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/listMateriel/1/0');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testListMateriePourRetour(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/listMateriel/1/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testAddCsv(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/addCsv/1/0');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testChangeUser(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/changeUser/1/0');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testEdit(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/edit/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDownloadRecap(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/downloadRacap/1');
        $this->assertEquals(404,$client->getResponse()->getStatusCode());
    }
    public function testRetour(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/retour');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testNouveauRetour(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/nouveauRetour');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testValidateRetour(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/validateRetour/1');
        $this->assertEquals(500,$client->getResponse()->getStatusCode());
    }
    public function testEnCours(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/retour/encours');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testDetails(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/retour/details/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testTerminerIntervention(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/retour/terminer/1');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }
    public function testRendre(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/rendre/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testFinisRendre(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/finisRendre/1');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }
    public function testAffectation(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/affectation/1');
        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }
    public function testTerminerAffectation(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/terminerAffectation/1');
        $this->assertEquals(302,$client->getResponse()->getStatusCode());
    }
    public function testSendMailRecap(){
        $client = static::createClient();

        $userRepository = static::$container->get(UserRepository::class);
        $testUser = $userRepository->findLogin('root');
        $client->loginUser($testUser[0]);

        $client->request('GET','/intervention/sendMailRecap/1');
        $this->assertEquals(500,$client->getResponse()->getStatusCode());
    }
}