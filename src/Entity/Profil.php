<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilRepository;
use ApiPlatform\Core\Api\FilterInterface;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 * @ApiFilter(BooleanFilter::class, properties={"isDeleted"})
 * @ApiResource(
 *      routePrefix="/admin",
 *      normalizationContext={"groups"={"profil:read"}},
 *      collectionOperations={
 *          "create_profil"={
 *              "method"="POST",
 *              "path"="/profils",
 *          },
 *          "get_profils"={
 *              "method"="GET",
 *              "path"="/profils"
 *          },
 *       },
 *      itemOperations={
 *          "get_profil"={
 *              "method"="GET",
 *              "path"="/profils/{id}",
 *              "requirements"={"id"="\d+"},
 *          },
 *          "update_profil"={
 *              "method"="PUT",
 *              "path"="/profils/{id}",
 *              "requirements"={"id"="\d+"},
 *          },
 *          "delete"={
 *              "method"="DELETE",
 *              "path"="/profils/{id}",
 *              "requirements"={"id"="\d+"},
 *          }
 *      },
 *     attributes={
 *          "security"="is_granted('ROLE_ADMIN')",
 *          "security_message"="Vous n'avez pas access à cette Ressource"
 *          }
 * ) 
 * @UniqueEntity("libelle")
 */
class Profil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"profilUsers:read","profil:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le libelle est obligatoire"),
     * @Groups({"user","profil:read","user:read"})
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profil", cascade="persist")
     * @Groups({"profil:read","profilUsers:read"})
     * @ApiSubresource
     */
    private $users;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"profil:read"})
     */
    private $isDeleted;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setProfil($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getProfil() === $this) {
                $user->setProfil(null);
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
