<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Intervention
 *
 * @ORM\Table(name="intervention", indexes={@ORM\Index(name="idLieu", columns={"idLieu"}), @ORM\Index(name="idReferent", columns={"idReferent"})})
 * @ORM\Entity
 */
class Intervention
{
    /**
     * @var int
     *
     * @ORM\Column(name="idIntervention", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idintervention;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dateIntervention", type="string", length=50, nullable=true)
     */
    private $dateintervention;

    /**
     * @var string|null
     *
     * @ORM\Column(name="statutInter", type="string", length=50, nullable=true)
     */
    private $statutinter;

    /**
     * @var \Referent
     *
     * @ORM\ManyToOne(targetEntity="Referent")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idReferent", referencedColumnName="idReferent")
     * })
     */
    private $idreferent;

    /**
     * @var \Lieu
     *
     * @ORM\ManyToOne(targetEntity="Lieu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLieu", referencedColumnName="idLieu")
     * })
     */
    private $idlieu;

    public function getIdintervention(): ?int
    {
        return $this->idintervention;
    }

    public function getDateintervention(): ?string
    {
        return $this->dateintervention;
    }

    public function setDateintervention(?string $dateintervention): self
    {
        $this->dateintervention = $dateintervention;

        return $this;
    }

    public function getStatutinter(): ?string
    {
        return $this->statutinter;
    }

    public function setStatutinter(?string $statutinter): self
    {
        $this->statutinter = $statutinter;

        return $this;
    }

    public function getIdreferent(): ?Referent
    {
        return $this->idreferent;
    }

    public function setIdreferent(?Referent $idreferent): self
    {
        $this->idreferent = $idreferent;

        return $this;
    }

    public function getIdlieu(): ?Lieu
    {
        return $this->idlieu;
    }

    public function setIdlieu(?Lieu $idlieu): self
    {
        $this->idlieu = $idlieu;

        return $this;
    }


}
