<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Specificite
 *
 * @ORM\Table(name="specificite")
 * @ORM\Entity
 * 
 * @UniqueEntity(
 * fields={"libellespe"},
 * message="Ce libellé existe déjà. "
 * )
 */
class Specificite
{
    /**
     * @var int
     *
     * @ORM\Column(name="idSpecificite", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idspecificite;

    /**
     * @var string|null
     *
     * @ORM\Column(name="libelleSpe", type="string", length=100, nullable=true)
     */
    private $libellespe;

    public function getIdspecificite(): ?int
    {
        return $this->idspecificite;
    }

    public function getLibellespe(): ?string
    {
        return $this->libellespe;
    }

    public function setLibellespe(?string $libellespe): self
    {
        $this->libellespe = $libellespe;

        return $this;
    }


}
