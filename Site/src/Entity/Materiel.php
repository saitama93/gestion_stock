<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Materiel
 *
 * @ORM\Table(name="materiel", indexes={@ORM\Index(name="idUser", columns={"idUser"}), @ORM\Index(name="idType", columns={"idType"}), @ORM\Index(name="idMarque", columns={"idMarque"}), @ORM\Index(name="idStatut", columns={"idStatut"}), @ORM\Index(name="idSpecificite", columns={"idSpecificite"}), @ORM\Index(name="idLieu", columns={"idLieu"})})
 * @ORM\Entity
 */
class Materiel
{
    /**
     * @var int
     *
     * @ORM\Column(name="idMateriel", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idmateriel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="numeroSerie", type="string", length=50, nullable=true)
     */
    private $numeroserie;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nomMateriel", type="string", length=200, nullable=true)
     */
    private $nommateriel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="motsCles", type="string", length=500, nullable=true)
     */
    private $motscles;

    /**
     *
     * @ORM\Column(name="date", type="datetime", nullable=false)
     */
    private $date;

    /**
     * @var string|null
     *
     * @ORM\Column(name="supprimer",type="boolean", nullable=false)
     */
    private $supprimer;

    /**
     * @var \Marque
     *
     * @ORM\ManyToOne(targetEntity="Marque")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMarque", referencedColumnName="idMarque")
     * })
     */
    private $idmarque;

    /**
     * @var \Lieu
     *
     * @ORM\ManyToOne(targetEntity="Lieu")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLieu", referencedColumnName="idLieu")
     * })
     */
    private $idlieu;

    /**
     * @var \Type
     *
     * @ORM\ManyToOne(targetEntity="Type")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idType", referencedColumnName="idType")
     * })
     */
    private $idtype;

    /**
     * @var \Specificite
     *
     * @ORM\ManyToOne(targetEntity="Specificite")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSpecificite", referencedColumnName="idSpecificite")
     * })
     */
    private $idspecificite;

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
     * @var \Statut
     *
     * @ORM\ManyToOne(targetEntity="Statut")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStatut", referencedColumnName="idStatut")
     * })
     */
    private $idstatut;

    public function getIdmateriel(): ?int
    {
        return $this->idmateriel;
    }

    public function getNumeroserie(): ?string
    {
        return $this->numeroserie;
    }

    public function setNumeroserie(?string $numeroserie): self
    {
        $this->numeroserie = $numeroserie;

        return $this;
    }

    public function getNommateriel(): ?string
    {
        return $this->nommateriel;
    }

    public function setNommateriel(?string $nommateriel): self
    {
        $this->nommateriel = $nommateriel;

        return $this;
    }

    public function getMotscles(): ?string
    {
        return $this->motscles;
    }

    public function setMotscles(?string $motscles): self
    {
        $this->motscles = $motscles;

        return $this;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getSupprimer(): ?string
    {
        return $this->supprimer;
    }

    public function setSupprimer(?string $supprimer): self
    {
        $this->supprimer = $supprimer;

        return $this;
    }

    public function getIdmarque(): ?Marque
    {
        return $this->idmarque;
    }

    public function setIdmarque(?Marque $idmarque): self
    {
        $this->idmarque = $idmarque;

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

    public function getIdtype(): ?Type
    {
        return $this->idtype;
    }

    public function setIdtype(?Type $idtype): self
    {
        $this->idtype = $idtype;

        return $this;
    }

    public function getIdspecificite(): ?Specificite
    {
        return $this->idspecificite;
    }

    public function setIdspecificite(?Specificite $idspecificite): self
    {
        $this->idspecificite = $idspecificite;

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
