<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Lieu
 *
 * @ORM\Table(name="lieu")
 * @ORM\Entity
 */
class Lieu
{
    /**
     * @var int
     *
     * @ORM\Column(name="idLieu", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idlieu;

    /**
     * @var string|null
     *
     * @ORM\Column(name="libelleLieu", type="string", length=200, nullable=true)
     */
    private $libellelieu;

    public function getIdlieu(): ?int
    {
        return $this->idlieu;
    }

    public function getLibellelieu(): ?string
    {
        return $this->libellelieu;
    }

    public function setLibellelieu(?string $libellelieu): self
    {
        $this->libellelieu = $libellelieu;

        return $this;
    }


}
