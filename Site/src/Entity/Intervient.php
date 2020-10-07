<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Intervient
 *
 * @ORM\Table(name="intervient", indexes={@ORM\Index(name="idStatut", columns={"idStatut"}), @ORM\Index(name="idLieuDepart", columns={"idLieuDepart"}), @ORM\Index(name="idLieuArrive", columns={"idLieuArrive"}), @ORM\Index(name="idMateriel", columns={"idMateriel"}), @ORM\Index(name="IDX_3F111A398F86B05A", columns={"idIntervention"})})
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
     *   @ORM\JoinColumn(name="idLieuDepart", referencedColumnName="idLieu")
     * })
     */
    private $idlieudepart;

    /**
     * @var \Lieu
     *
     * @ORM\ManyToOne(targetEntity="Lieu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLieuArrive", referencedColumnName="idLieu")
     * })
     */
    private $idlieuarrive;

    /**
     * @var \Statut
     *
     * @ORM\ManyToOne(targetEntity="Statut")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStatut", referencedColumnName="idStatut")
     * })
     */
    private $idstatut;

    public function getDateaffectation(): ?string
    {
        return $this->dateaffectation;
    }

    public function setDateaffectation(?string $dateaffectation): self
    {
        $this->dateaffectation = $dateaffectation;

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

    public function getIdlieudepart(): ?Lieu
    {
        return $this->idlieudepart;
    }

    public function setIdlieudepart(?Lieu $idlieudepart): self
    {
        $this->idlieudepart = $idlieudepart;

        return $this;
    }

    public function getIdlieuarrive(): ?Lieu
    {
        return $this->idlieuarrive;
    }

    public function setIdlieuarrive(?Lieu $idlieuarrive): self
    {
        $this->idlieuarrive = $idlieuarrive;

        return $this;
    }

    public function getIdstatut(): ?Statut
    {
        return $this->idstatut;
    }

    public function setIdstatut(?Statut $idstatut): self
    {
        $this->idstatut = $idstatut;

        return $this;
    }


}
