<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FormateurRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=FormateurRepository::class)
 * @ApiResource(
 *      attributes={
 *          "security"="is_granted('ROLE_FORMATEUR')",
 *          "security_message"="Vous n'avez pas access à cette Ressource"
 *      },
 *      collectionOperations={
    *          "create_apprenant"={
    *              "method"="POST",
    *              "path"="/apprenants"
    *          },
    *          "get_apprenants"={
    *              "method"="GET",
    *              "path"="/apprenants"
    *          }
 *      },
 *      itemOperations={
 *          "update_formateur"={
 *              "deserialize"=false,
*              "method"="PUT",
*              "path"="/formateurs{/id}"  
 *          },
 *          "update_apprenant"={
 *              "deserialize"=false,
*              "method"="PUT",
*              "path"="/apprenants/{id}"  
 *          },
 *          "get_apprenant"={
 *              "method"="GET",
 *              "path"="/apprenants/{id}"
 *          },
 *          "get_formateur"={
 *              "method"="GET",
 *              "path"="/formateurs/{id}"
 *          }
 *      }      
 *      
 * )
 */
class Formateur extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity=Groupe::class, mappedBy="formateur")
     */
    private $groupes;

    /**
     * @ORM\ManyToMany(targetEntity=Promo::class, mappedBy="formateur")
     */
    private $promos;

    public function __construct()
    {
        $this->groupes = new ArrayCollection();
        $this->promos = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Groupe[]
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes[] = $groupe;
            $groupe->addFormateur($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->removeElement($groupe)) {
            $groupe->removeFormateur($this);
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
            $promo->addFormateur($this);
        }

        return $this;
    }

    public function removePromo(Promo $promo): self
    {
        if ($this->promos->removeElement($promo)) {
            $promo->removeFormateur($this);
        }

        return $this;
    }
}
