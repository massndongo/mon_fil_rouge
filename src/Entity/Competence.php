<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompetenceRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiFilter(BooleanFilter::class, properties={"isDeleted"})
 * @ApiResource(
 *      
 *      routePrefix="/admin",
 *      normalizationContext={"groups"={"competence:read"}},
 *      denormalizationContext={"groups"={"competence:write"}},
 *      collectionOperations={
 *          "create_competence"={
 *              "method"="POST",
 *              "path"="competences",
 *          },
 *          "get_all_competences"={
 *              "method"="GET",
 *              "path"="/all/competences"
 *          }
 *      },
 *      itemOperations={
 *          "get_competence"={
 *              "method"="GET",
 *              "path"="/all/competences/{id}"
 *          },
 *          "set_competence"={
 *              "method"="PUT",
 *              "path"="/competences/{id}"
 *          },
 *          "delete_competence"={
 *              "method"="DELETE",
 *              "path"="/competences/{id}"
 *          }
 *      }
 * )
 * @ORM\Entity(repositoryClass=CompetenceRepository::class)
 * @UniqueEntity("libelle",message="Le libelle est unique")
 */
class Competence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"competence:read","competenceIngrpe:read","grpecompetence:read","grpecompetence:write"})
     */
    private $id;

    /**
     * @Groups({"competence:write","competenceIngrpe:read","competence:read","grpecompetence:read","grpeCompRef:read","compRef:read"})
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le libelle est obligatoire")
     */
    private $libelle;

    /**
     * @Groups({"competence:write","competence:read","competenceIngrpe:read","grpecompetence:read","grpeCompRef:read","compRef:read"})
     * @ORM\Column(type="string", length=255)
     */
    private $descriptif;

    /**
     * @ORM\ManyToMany(targetEntity=GroupeCompetence::class, mappedBy="competences")
     * @Groups({"competence:write","competence:read"})
     * @Assert\NotBlank(message="Le Groupe de competence est obligatoire")
     */
    private $groupeCompetences;

    /**
     * @Groups({"competence:read","grpecompetence:read"})
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    /**
     * @Groups({"competence:write","competenceIngrpe:read","competence:read"})
     * @ORM\OneToMany(targetEntity=Niveau::class, mappedBy="competence",cascade={"persist"})    
     * @Assert\Count(
     *      min = 3,
     *      max = 3,
     *      minMessage = "Donnez 3 Niveaux",
     *      maxMessage = "Vous ne pouvez pas depasser {{ 3 }} Niveaux"
     * )
     */
    private $niveaux;

    public function __construct()
    {
        $this->groupeCompetences = new ArrayCollection();
        $this->niveaux = new ArrayCollection();
        $this->isDeleted = false;
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
            $groupeCompetence->addCompetence($this);
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
