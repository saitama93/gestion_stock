<?php
namespace App\Tests\Entity;

use App\Entity\Intervient;
use PHPUnit\Framework\TestCase;

class IntervientTest extends TestCase
{
    private $intervient;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->intervient = new Intervient();
        parent::__construct($name, $data, $dataName);
    }
    public function testGetStatutLibelleDisponible(){
        $this->intervient->setStatut(0);
        $this->assertEquals('Disponible',$this->intervient->getStatutLibelle());
    }
    public function testGetStatutLibelleProduction(){
        $this->intervient->setStatut(1);
        $this->assertEquals('En production',$this->intervient->getStatutLibelle());
    }
    public function testGetStatutLibelleSAV(){
        $this->intervient->setStatut(2);
        $this->assertEquals('SAV',$this->intervient->getStatutLibelle());
    }
    public function testGetStatutLibelleImmobilise(){
        $this->intervient->setStatut(3);
        $this->assertEquals('Immobilisé',$this->intervient->getStatutLibelle());
    }
    public function testGetStatutLibelleAvecMauvaisStatut(){
        $this->intervient->setStatut(4);
        $this->assertEquals('inconnus',$this->intervient->getStatutLibelle());
    }
}