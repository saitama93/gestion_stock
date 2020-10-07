<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Type
 *
 * @ORM\Table(name="type")
 * @ORM\Entity
 */
class Type
{
    /**
     * @var int
     *
     * @ORM\Column(name="idType", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idtype;

    /**
     * @var string|null
     *
     * @ORM\Column(name="libelleType", type="string", length=100, nullable=true)
     */
    private $libelletype;

    public function getIdtype(): ?int
    {
        return $this->idtype;
    }

    public function getLibelletype(): ?string
    {
        return $this->libelletype;
    }

    public function setLibelletype(?string $libelletype): self
    {
        $this->libelletype = $libelletype;

        return $this;
    }


}
