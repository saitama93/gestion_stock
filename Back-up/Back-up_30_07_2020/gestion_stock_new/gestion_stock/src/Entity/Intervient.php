<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Intervient
 *
 * @ORM\Table(name="intervient", indexes={@ORM\Index(name="idMateriel", columns={"idMateriel"}), @ORM\Index(name="idLieu", columns={"idLieu"}), @ORM\Index(name="IDX_3F111A398F86B05A", columns={"idIntervention"})})
 * @ORM\Entity
 */
class Intervient
{
    /**
     * @var string|null
     *
     * @ORM\Column(name="dateAffectation", type="string", length=50, nullable=true)
     */
    private $dateaffectation;

    /**
     * @var int|null
     *
     * @ORM\Column(name="statut", type="integer", nullable=true)
     */
    private $statut;

    /**
     * @var \Intervention
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Intervention")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idIntervention", referencedColumnName="idIntervention")
     * })
     */
    private $idintervention;

    /**
     * @var \Materiel
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="Materiel")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMateriel", referencedColumnName="idMateriel")
     * })
     */
    private $idmateriel;

    /**
     * @var \Lieu
     *
     * @ORM\ManyToOne(targetEntity="Lieu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLieu", referencedColumnName="idLieu")
     * })
     */
    private $idlieu;

    public function getDateaffectation(): ?string
    {
        return $this->dateaffectation;
    }

    public function setDateaffectation(?string $dateaffectation): self
    {
        $this->dateaffectation = $dateaffectation;

        return $this;
    }

    public function getStatut(): ?int
    {
        return $this->statut;
    }

    public function getStatutLibelle(): string
    {
        switch ($this->statut){
            case 0:
                return 'Disponible';
            case 1 :
                return 'En production';
            case 2:
                return 'SAV';
            case 3:
                return 'Immobilisé';
            default:
                return 'inconnus';
        }
    }

    public function setStatut(?int $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getIdintervention(): ?Intervention
    {
        return $this->idintervention;
    }

    public function setIdintervention(?Intervention $idintervention): self
    {
        $this->idintervention = $idintervention;

        return $this;
    }

    public function getIdmateriel(): ?Materiel
    {
        return $this->idmateriel;
    }

    public function setIdmateriel(?Materiel $idmateriel): self
    {
        $this->idmateriel = $idmateriel;

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
