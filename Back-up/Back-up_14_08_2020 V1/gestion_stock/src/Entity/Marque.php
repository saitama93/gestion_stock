<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Marque
 *
 * @ORM\Table(name="marque")
 * @ORM\Entity
 */
class Marque
{
    /**
     * @var int
     *
     * @ORM\Column(name="idMarque", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idmarque;

    /**
     * @var string|null
     *
     * @ORM\Column(name="libelleMarque", type="string", length=50, nullable=true)
     */
    private $libellemarque;

    public function getIdmarque(): ?int
    {
        return $this->idmarque;
    }

    public function getLibellemarque(): ?string
    {
        return $this->libellemarque;
    }

    public function setLibellemarque(?string $libellemarque): self
    {
        $this->libellemarque = $libellemarque;

        return $this;
    }


}
