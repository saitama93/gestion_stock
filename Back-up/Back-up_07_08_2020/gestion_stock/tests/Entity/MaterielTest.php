<?php
namespace App\Tests\Entity;

use App\Entity\Lieu;
use App\Entity\Marque;
use App\Entity\Materiel;
use App\Entity\Referent;
use App\Entity\Specificite;
use App\Entity\Type;
use PHPUnit\Framework\TestCase;

class MaterielTest extends TestCase
{
    private $materiel ;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->materiel = new Materiel();
        parent::__construct($name, $data, $dataName);
    }

    public function testNumeroSerie(){
        $this->materiel->setNumeroserie('1545test');
        $this->assertIsString($this->materiel->getNumeroserie());
    }
    public function testNomMateriel(){
        $this->materiel->setNommateriel('Nom');
        $this->assertIsString($this->materiel->getNommateriel());
    }
    public function testMotscles(){
        $this->materiel->setMotscles('Nom');
        $this->assertIsString($this->materiel->getMotscles());
    }
    public function testSupprimer(){
        $this->assertNull($this->materiel->getSupprimer());
        $this->materiel->setSupprimer('test');
        $this->assertIsString($this->materiel->getSupprimer());
    }
    public function testMarque(){
        $this->materiel->setIdmarque(new Marque());
        $this->assertInstanceOf(Marque::class,$this->materiel->getIdmarque());
    }
    public function testLieu(){
        $this->materiel->setIdlieu(new Lieu());
        $this->assertInstanceOf(Lieu::class,$this->materiel->getIdlieu());
    }
    public function testType(){
        $this->materiel->setIdtype(new Type());
        $this->assertInstanceOf(Type::class,$this->materiel->getIdtype());
    }
    public function testSpecificite(){
        $this->materiel->setIdspecificite(new Specificite());
        $this->assertInstanceOf(Specificite::class,$this->materiel->getIdspecificite());
    }
}