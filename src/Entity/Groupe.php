<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=GroupeRepository::class)
 * @ApiResource(
 *      attributes={
 *          "denormalization_context"={"groups"={"groupe:write"}}
 *      },
 *      routePrefix="/admin",
 *      collectionOperations={
 *          "create_groupe"={
 *              "denormalization_context"={"groups"={"groupe:write"}},
 *              "method"="POST",
 *              "path"="/groupes"
 *          },
 *          "get_all_groupes"={
 *              "method"="GET",
 *              "path"="/groupes"
 *          },
 *          "get_apprenants_in_groupes"={
 *              "method"="GET",
 *              "path"="/groupes/apprenants"
 *          }
 *      },
 *      itemOperations={
 *          "get_groupe"={
 *              "method"="GET",
 *              "path"="/groupes/{id}"
 *          },
 *          "set_groupe"={
 *              "method"="PUT",
 *              "path"="/groupes/{id}"
 *          },
 *          "del_groupe"={
 *              "method"="DELETE",
 *              "path"="/groupes/{id}/apprenants"
 *          }
 *      }
 * )
 */
class Groupe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"groupe:write"})
     */
    private $nom;

    /**
     * @ORM\Column(type="date")
     * @Groups({"groupe:write"})
     */
    private $dateCreation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $statut;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"groupe:write"})
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity=Formateur::class, inversedBy="groupes",cascade={"persist"})
     * @Groups({"groupe:write"})
     */
    private $formateur;

    /**
     * @ORM\ManyToMany(targetEntity=Apprenant::class, inversedBy="groupes",cascade={"persist"})
     * @Groups({"groupe:write"})
     */
    private $apprenant;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    /**
     * @ORM\ManyToOne(targetEntity=Promo::class, inversedBy="groupes")
     * @Groups({"groupe:write"})
     */
    private $promo;

    public function __construct()
    {
        $this->formateur = new ArrayCollection();
        $this->apprenant = new ArrayCollection();
        $this->isDeleted = false;
        $this->statut = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|Formateur[]
     */
    public function getFormateur(): Collection
    {
        return $this->formateur;
    }

    public function addFormateur(Formateur $formateur): self
    {
        if (!$this->formateur->contains($formateur)) {
            $this->formateur[] = $formateur;
        }

        return $this;
    }

    public function removeFormateur(Formateur $formateur): self
    {
        $this->formateur->removeElement($formateur);

        return $this;
    }

    /**
     * @return Collection|Apprenant[]
     */
    public function getApprenant(): Collection
    {
        return $this->apprenant;
    }

    public function addApprenant(Apprenant $apprenant): self
    {
        if (!$this->apprenant->contains($apprenant)) {
            $this->apprenant[] = $apprenant;
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        $this->apprenant->removeElement($apprenant);

        return $this;
    }

    public function getIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function getPromo(): ?Promo
    {
        return $this->promo;
    }

    public function setPromo(?Promo $promo): self
    {
        $this->promo = $promo;

        return $this;
    }
}
