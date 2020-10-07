<?php
namespace App\Tests\Entity;

use App\Entity\Referent;
use PHPUnit\Framework\TestCase;

class ReferentTest extends TestCase
{
    private $referent;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->referent = new Referent();
        parent::__construct($name, $data, $dataName);
    }
    public function testNom(){
        $this->referent->setNom('test');
        $this->assertIsString($this->referent->getNom());
    }
    public function testPrenom(){
        $this->referent->setPrenom('test');
        $this->assertIsString($this->referent->getPrenom());
    }
    public function testEquipe(){
        $this->referent->setEquipe('test');
        $this->assertIsString($this->referent->getEquipe());
    }
    public function testMailEquipe(){
        $this->referent->setMailequipe('test');
        $this->assertIsString($this->referent->getMailequipe());
    }

    public function testPresent(){
        $this->referent->setPresent(0);
        $this->assertIsInt($this->referent->getPresent());
    }
}