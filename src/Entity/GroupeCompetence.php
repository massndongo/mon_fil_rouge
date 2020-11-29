<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\GroupeCompetenceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *      denormalizationContext={"groups"={"grpecompetence:write"}},
 *      collectionOperations={
*          "create_grpecompetences"={
*              "method"="POST",
*              "path"="/admin/grpecompetences"
            },
 *          "get_all_grpecompetence"={
 *              "method"="GET",
 *              "path"="/admin/grpecompetences"
 *          }
 *      },
 *      itemOperations={
 *          "get_grpecompetence"={
 *              "method"="GET",
 *              "path"="/admin/grpecompetences/{id}"
 *          },
 *          "delete_competence"={
 *              "method"="DELETE",
 *              "path"="/admin/grpecompetences/{id}"
 *          }
 *      }
 * )
 * @ORM\Entity(repositoryClass=GroupeCompetenceRepository::class)
 */
class GroupeCompetence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"competence:write","competence:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"competence:read","grpecompetence:write"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"competence:read","grpecompetence:write"})
     */
    private $descriptif;

    /**
     * @ORM\ManyToOne(targetEntity=Admin::class, inversedBy="groupeCompetences")
     */
    private $administrateur;

    /**
     * @ORM\ManyToMany(targetEntity=Competence::class, inversedBy="groupeCompetences", cascade={"persist"})
     * @Groups({"grpecompetence:write"})
     */
    private $competences;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"grpecompetence:write"})
     */
    private $isDeleted;

    /**
     * @ORM\ManyToMany(targetEntity=Referentiel::class, mappedBy="groupeCompetence")
     */
    private $referentiels;

    public function __construct()
    {
        $this->competences = new ArrayCollection();
        $this->referentiels = new ArrayCollection();
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

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(string $descriptif): self
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    public function getAdministrateur(): ?Admin
    {
        return $this->administrateur;
    }

    public function setAdministrateur(?Admin $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    /**
     * @return Collection|Competence[]
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
    }

    public function addCompetence(Competence $competence): self
    {
        if (!$this->competences->contains($competence)) {
            $this->competences[] = $competence;
            $competence->addGroupeCompetence($this);
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): self
    {
        $this->competences->removeElement($competence);

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
     * @return Collection|Referentiel[]
     */
    public function getReferentiels(): Collection
    {
        return $this->referentiels;
    }

    public function addReferentiel(Referentiel $referentiel): self
    {
        if (!$this->referentiels->contains($referentiel)) {
            $this->referentiels[] = $referentiel;
            $referentiel->addGroupeCompetence($this);
        }

        return $this;
    }

    public function removeReferentiel(Referentiel $referentiel): self
    {
        if ($this->referentiels->removeElement($referentiel)) {
            $referentiel->removeGroupeCompetence($this);
        }

        return $this;
    }
}
