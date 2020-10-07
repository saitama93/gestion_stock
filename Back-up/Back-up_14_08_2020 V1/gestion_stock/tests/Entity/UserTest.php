<?php
namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $user;

    public function __construct($name = null, array $data = [], $dataName = '')
    {
        $this->user = new User();
        parent::__construct($name, $data, $dataName);
    }

    public function testUsername(){
        $this->assertNull($this->user->getUsername());
        $this->user->setUsername('test');
        $this->assertIsString($this->user->getUsername());
    }

    public function testPassword(){
        $this->assertNull($this->user->getPassword());
        $this->user->setPassword('test');
        $this->assertIsString($this->user->getPassword());
    }

    public function testPlainPassword(){
        $this->assertNull($this->user->getPlainPassword());
        $this->user->setPlainPassword('test');
        $this->assertIsString($this->user->getPlainPassword());
    }
    public function testRoles(){
        $this->assertIsArray($this->user->getRoles());
        $this->user->setRoles(array('test'));
        $this->assertIsArray($this->user->getRoles());
        $this->assertEquals(2,sizeof($this->user->getRoles()));
    }
}