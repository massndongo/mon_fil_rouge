<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use App\Repository\ProfilSortieRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;

/**
 * @ApiFilter(BooleanFilter::class, properties={"isDeleted"})
 * @ApiResource(
 *      routePrefix="/admin",
 *      normalizationContext={"groups"={"profilSortie:read"}},
 *      collectionOperations={
 *              "get_all_profil_sortie"={
 *                  "method"="GET",
 *                  "path"="/profilsorties"
 *              },
 *              "get_student_in_promo_by_profil_sortie"={
 *                  "method"="GET",
 *                  "path"="/promo/{id}/profilsorties",
 *              },
 *              "create_profil_sortie"={
 *                  "method"="POST",
 *                  "path"="/profilsorties"
 *              }
 *      },
 *      itemOperations={
 *              "get_one_profil_sortie"={
 *                  "method"="GET",
 *                  "path"="/profilsorties/{id}",
 *              },
 *              "get_student_profil_sortie_promo"={
 *                  "method"="GET",
 *                  "path"="/promo/{id}/profilsorties/{idP}"
 *              },
 *              "update_profil_sortie"={
 *                  "method"="PUT",
 *                  "path"="/profilsorties/{id}"
 *              }
 *          
 *      },
 *     attributes={
 *          "pagination_items_per_page"=6
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
     * @Groups({"profilSortie:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le libelle est obligatoire")
     * @Groups({"profilSortie:read"})
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Apprenant::class, mappedBy="profilSortie")
     * @Groups({"profilSortie:read"})
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
