<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statut
 *
 * @ORM\Table(name="statut")
 * @ORM\Entity
 */
class Statut
{
    /**
     * @var int
     *
     * @ORM\Column(name="idStatut", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idstatut;

    /**
     * @var string|null
     *
     * @ORM\Column(name="libelleStatut", type="string", length=50, nullable=true)
     */
    private $libellestatut;

    public function getIdstatut(): ?int
    {
        return $this->idstatut;
    }

    public function getLibellestatut(): ?string
    {
        return $this->libellestatut;
    }

    public function setLibellestatut(?string $libellestatut): self
    {
        $this->libellestatut = $libellestatut;

        return $this;
    }


}
