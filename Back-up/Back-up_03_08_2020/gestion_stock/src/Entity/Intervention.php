<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Intervention
 *
 * @ORM\Table(name="intervention", indexes={@ORM\Index(name="idLieu", columns={"idLieu"}), @ORM\Index(name="idUser", columns={"idUser"})})
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
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="idUser")
     * })
     */
    private $iduser;

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

    public function getIduser(): ?User
    {
        return $this->iduser;
    }

    public function setIduser(?User $iduser): self
    {
        $this->iduser = $iduser;

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
