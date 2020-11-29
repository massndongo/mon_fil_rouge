<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompetenceRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      denormalizationContext={"groups"={"competence:write"}},
 *      normalizationContext={"groups"={"competence:read"}},
 *      collectionOperations={
*          "create_competences"={
*              "method"="POST",
*              "path"="/admin/competences"
            },
 *          "get_all_competence"={
 *              "method"="GET",
 *              "path"="/admin/competences"
 *          }
 *      },
 *      itemOperations={
 *          "get_competence"={
 *              "method"="GET",
 *              "path"="/admin/competences/{id}"
 *          },
 *          "delete_competence"={
 *              "method"="DELETE",
 *              "path"="/admin/competences/{id}"
 *          }
 *      }
 * )
 * @ORM\Entity(repositoryClass=CompetenceRepository::class)
 */
class Competence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"competence:read","grpecompetence;write"})
     */
    private $id;

    /**
     * @Groups({"competence:write","competence:read"})
     * @ORM\ManyToMany(targetEntity=GroupeCompetence::class, mappedBy="competences", cascade={"persist"})
     */
    private $groupeCompetences;

    /**
     * @Groups({"competence:write","competence:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @Groups({"competence:write","competence:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $descriptif;

    /**
     * @Groups({"competence:write","competence:read"})
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    /**
     * @Groups({"competence:write","competence:read"})
     * @ORM\OneToMany(targetEntity=Niveau::class, mappedBy="competence")
     */
    private $niveaux;

    public function __construct()
    {
        $this->groupeCompetences = new ArrayCollection();
        $this->niveaux = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|GroupeCompetence[]
     */
    public function getGroupeCompetences(): Collection
    {
        return $this->groupeCompetences;
    }

    public function addGroupeCompetence(GroupeCompetence $groupeCompetence): self
    {
        if (!$this->groupeCompetences->contains($groupeCompetence)) {
            $this->groupeCompetences[] = $groupeCompetence;
        }

        return $this;
    }

    public function removeGroupeCompetence(GroupeCompetence $groupeCompetence): self
    {
        if ($this->groupeCompetences->removeElement($groupeCompetence)) {
            $groupeCompetence->removeCompetence($this);
        }

        return $this;
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

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(string $descriptif): self
    {
        $this->descriptif = $descriptif;

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
     * @return Collection|Niveau[]
     */
    public function getNiveaux(): Collection
    {
        return $this->niveaux;
    }

    public function addNiveau(Niveau $niveau): self
    {
        if (!$this->niveaux->contains($niveau)) {
            $this->niveaux[] = $niveau;
            $niveau->setCompetence($this);
        }

        return $this;
    }

    public function removeNiveau(Niveau $niveau): self
    {
        if ($this->niveaux->removeElement($niveau)) {
            // set the owning side to null (unless already changed)
            if ($niveau->getCompetence() === $this) {
                $niveau->setCompetence(null);
            }
        }

        return $this;
    }
}
