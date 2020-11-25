<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdminRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;

/**
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 * @ORM\Table(name="`admin`") 
 * @ApiFilter(BooleanFilter::class, properties={"isDeleted"})
 * @ApiResource(
 *      normalizationContext={"groups"={"user:read"}},
 *      denormalizationContext={"groups"={"user"}},
 *      attributes={
 *          "security"="is_granted('ROLE_ADMIN')",
 *          "security_message"="Vous n'avez pas access à cette Ressource"
 *      },
 *      collectionOperations={
*          "create_admin"={
*              "method"="POST",
*              "path"="/admin"
 *          },
 *          "get_admin"={
 *              "method"="GET",
 *              "path"="/admin"
 *          }
 *      },
 *      itemOperations={
 *          "delete_admin"={
*              "method"="DELETE",
*              "path"="/admin/{id}"  
 *          },
 *          "update_admin"={
 *              "deserialize"=false,
*              "method"="PUT",
*              "path"="/admin/{id}"  
 *          },
 *          "get_admin"={
 *              "method"="GET",
 *              "path"="/admin/{id}"
 *          }
 *      }      
 * )
 */
class Admin extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity=GroupeCompetence::class, mappedBy="administrateur")
     */
    private $groupeCompetences;

    /**
     * @ORM\OneToMany(targetEntity=Promo::class, mappedBy="admin")
     */
    private $promos;

    public function __construct()
    {
        $this->groupeCompetences = new ArrayCollection();
        $this->promos = new ArrayCollection();
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
            $groupeCompetence->setAdministrateur($this);
        }

        return $this;
    }

    public function removeGroupeCompetence(GroupeCompetence $groupeCompetence): self
    {
        if ($this->groupeCompetences->removeElement($groupeCompetence)) {
            // set the owning side to null (unless already changed)
            if ($groupeCompetence->getAdministrateur() === $this) {
                $groupeCompetence->setAdministrateur(null);
            }
        }

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
            $promo->setAdmin($this);
        }

        return $this;
    }

    public function removePromo(Promo $promo): self
    {
        if ($this->promos->removeElement($promo)) {
            // set the owning side to null (unless already changed)
            if ($promo->getAdmin() === $this) {
                $promo->setAdmin(null);
            }
        }

        return $this;
    }
}
