<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReferentielRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ReferentielRepository::class)
 * @ApiResource(
 *      routePrefix="/admin",
 *      attributes={
 *          "normalization_context"={"groups"={"ref:read","grpeCompRef:read","compRef:read"}}
 *      },
 *      collectionOperations={
*          "create_referentiels"={
*              "method"="POST",
*              "path"="/referentiels"
            },
 *          "get_all_referentiels"={
 *              "normalization_context"={"groups"={"ref:read"}},
 *              "method"="GET",
 *              "path"="/referentiels"
 *          },
 *          "get_all_grpecompetences_in_referentiel"={
 *              "normalization_context"={"groups"={"grpeCompRef:read"}},
 *              "method"="GET",
 *              "path"="/referentiels/grpecompetences"
 *          }
 *      },
 *      itemOperations={
 *          "get_referentiel"={
 *              "method"="GET",
 *              "path"="/referentiels/{id}"
 *          },
 *          "get_competences_grpecompetences_in_referentiel"={
 *              "normalization_context"={"groups"={"compRef:read"}},
 *              "method"="GET",
 *              "path"="/referentiels/{idR}/grpecompetences/{id}"
 *          },
 *          "delete_referentiel"={
 *              "method"="DELETE",
 *              "path"="/referentiels/{id}"
 *          },
 *          "set_referentiel"={
 *              "method"="PUT",
 *              "path"="/referentiels/{id}"
 *          }
 *      }
 *      
 * )
 * @UniqueEntity("libelle",message="Le libelle doit etre unique")
 */
class Referentiel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"ref:read","compRef:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Groups({"ref:read"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Groups({"ref:read"})
     */
    private $presentation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Groups({"ref:read"})
     */
    private $critereEvaluation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Groups({"ref:read"})
     */
    private $critereAdmission;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    /**
     * @ORM\ManyToMany(targetEntity=GroupeCompetence::class, inversedBy="referentiels")
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Groups({"grpeCompRef:read","compRef:read"})
     */
    private $groupeCompetence;

    /**
     * @ORM\OneToMany(targetEntity=Promo::class, mappedBy="referentiel")
     */
    private $promos;

    /**
     * @ORM\Column(type="blob", nullable=true)
     * @Assert\NotBlank(message="Ce champ est obligatoire")
     * @Groups({"ref:read"})
     */
    private $programme;

    public function __construct()
    {
        $this->groupeCompetence = new ArrayCollection();
        $this->promos = new ArrayCollection();
        $this->isDeleted = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getCritereEvaluation(): ?string
    {
        return $this->critereEvaluation;
    }

    public function setCritereEvaluation(string $critereEvaluation): self
    {
        $this->critereEvaluation = $critereEvaluation;

        return $this;
    }

    public function getCritereAdmission(): ?string
    {
        return $this->critereAdmission;
    }

    public function setCritereAdmission(string $critereAdmission): self
    {
        $this->critereAdmission = $critereAdmission;

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

    /**
     * @return Collection|GroupeCompetence[]
     */
    public function getGroupeCompetence(): Collection
    {
        return $this->groupeCompetence;
    }

    public function addGroupeCompetence(GroupeCompetence $groupeCompetence): self
    {
        if (!$this->groupeCompetence->contains($groupeCompetence)) {
            $this->groupeCompetence[] = $groupeCompetence;
        }

        return $this;
    }

    public function removeGroupeCompetence(GroupeCompetence $groupeCompetence): self
    {
        $this->groupeCompetence->removeElement($groupeCompetence);

        return $this;
    }

    /**
     * @return Collection|Promo[]
     */
    public function getPromos(): Collection
    {
        return $this->promos;
    }

    public function addPromo(Promo $promo): self
    {
        if (!$this->promos->contains($promo)) {
            $this->promos[] = $promo;
            $promo->setReferentiel($this);
        }

        return $this;
    }

    public function removePromo(Promo $promo): self
    {
        if ($this->promos->removeElement($promo)) {
            // set the owning side to null (unless already changed)
            if ($promo->getReferentiel() === $this) {
                $promo->setReferentiel(null);
            }
        }

        return $this;
    }

    public function getProgramme()
    {
        return base64_encode(stream_get_contents($this->programme));
    }

    public function setProgramme($programme): self
    {
        $this->programme = $programme;

        return $this;
    }
}
