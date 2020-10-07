<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Referent
 *
 * @ORM\Table(name="referent")
 * @ORM\Entity
 */
class Referent
{
    /**
     * @var int
     *
     * @ORM\Column(name="idReferent", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idreferent;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=true)
     * @Assert\Regex(
     *     pattern="/[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/",
     *     message="Ne doit pas contenir de chiffre ou de charactère spécial"
     * )
     */
    private $nom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="prenom", type="string", length=50, nullable=true)
     * @Assert\Regex(
     *     pattern="/[a-zA-ZàáâäãåąčćęèéêëėįìíîïłńòóôöõøùúûüųūÿýżźñçčšžÀÁÂÄÃÅĄĆČĖĘÈÉÊËÌÍÎÏĮŁŃÒÓÔÖÕØÙÚÛÜŲŪŸÝŻŹÑßÇŒÆČŠŽ∂ð ,.'-]+$/",
     *     message="Ne doit pas contenir de chiffre ou de charactère spécial"
     * )
     */
    private $prenom;

    /**
     * @var string|null
     *
     * @ORM\Column(name="equipe", type="string", length=50, nullable=true)
     */
    private $equipe;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mailEquipe", type="string", length=100, nullable=true)
     */
    private $mailequipe;

    /**
     * @var int|null
     *
     * @ORM\Column(name="present", type="integer", nullable=true)
     */
    private $present;

    public function getIdreferent(): ?int
    {
        return $this->idreferent;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getEquipe(): ?string
    {
        return $this->equipe;
    }

    public function setEquipe(?string $equipe): self
    {
        $this->equipe = $equipe;

        return $this;
    }

    public function getMailequipe(): ?string
    {
        return $this->mailequipe;
    }

    public function setMailequipe(?string $mailequipe): self
    {
        $this->mailequipe = $mailequipe;

        return $this;
    }

    public function getPresent(): ?int
    {
        return $this->present;
    }

    public function setPresent(?int $present): self
    {
        $this->present = $present;

        return $this;
    }


}
