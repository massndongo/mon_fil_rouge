<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use Symfony\Component\Validator\Constraints as Assert;
use App\Repository\ProfilSortieRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;

/**
 * @ApiFilter(BooleanFilter::class, properties={"isDeleted"})
 * @ApiResource(
 *      routePrefix="/admin",
 *      collectionOperations={
 *              "get_all_profil_sortie"={
 *                  "method"="GET",
 *                  "path"="/profilsorties",
 *                  "security"="is_granted('ROLE_CM')",
 *                  "security_message"="Vous n'avez pas access à cette Ressource"
 *              },
 *              "get_student_in_promo_by_profil_sortie"={
 *                  "method"="GET",
 *                  "path"="/promo/{id}/profilsorties",
 *                  "security"="is_granted('ROLE_CM')",
 *                  "security_message"="Vous n'avez pas access à cette Ressource"
 *              },
 *              "create_profil_sortie"={
 *                  "method"="POST",
 *                  "path"="/profilsorties",
 *                  "security"="is_granted('ROLE_CM')",
 *                  "security_message"="Vous n'avez pas access à cette Ressource"
 *              }
 *      },
 *      itemOperations={
 *              "get_one_profil_sortie"={
 *                  "method"="GET",
 *                  "path"="/profilsorties/{id}",
 *                  "security"="is_granted('ROLE_CM')",
 *                  "security_message"="Vous n'avez pas access à cette Ressource"
 *              },
 *              "get_student_profil_sortie_promo"={
 *                  "method"="GET",
 *                  "path"="/promo/{id}/profilsorties/{idP}",
 *                  "security"="is_granted('ROLE_FORMATEUR')",
 *                  "security_message"="Vous n'avez pas access à cette Ressource"
 *              },
 *              "update_profil_sortie"={
 *                  "method"="PUT",
 *                  "path"="/profilsorties/{id}",
 *                  "security"="is_granted('ROLE_FORMATEUR')",
 *                  "security_message"="Vous n'avez pas access à cette Ressource"
 *              }
 *          
 *      },
 *     attributes={
 *          "pagination_items_per_page"=2
 *          },
 *
 * )
 * @ORM\Entity(repositoryClass=ProfilSortieRepository::class)
 */
class ProfilSortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le libelle est obligatoire"),
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Apprenant::class, mappedBy="profilSortie")
     */
    private $apprenant;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isDeleted;

    public function __construct()
    {
        $this->apprenant = new ArrayCollection();
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
            $apprenant->setProfilSortie($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        if ($this->apprenant->removeElement($apprenant)) {
            // set the owning side to null (unless already changed)
            if ($apprenant->getProfilSortie() === $this) {
                $apprenant->setProfilSortie(null);
            }
        }

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
}
