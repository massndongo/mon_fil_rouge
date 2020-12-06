<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TagRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 *      normalizationContext={"groups"={"tag:read"}},
 *      denormalizationContext={"groups"={"tag:write"}},
 *      collectionOperations={
 *          "get_all_tag"={
 *              "method"="GET",
 *              "path"="/admin/tags"
 *          },
*          "create_tags"={
*              "method"="POST",
*              "path"="/admin/tags"
            }
 *      },
 *      itemOperations={
 *          "get_tag"={
 *              "method"="GET",
 *              "path"="/admin/tags/{id}"
 *          },
 *          "set_tag"={
 *              "method"="PUT",
 *              "path"="/admin/tags/{id}"
 *          },
 *          "delete_tag"={
 *              "method"="DELETE",
 *              "path"="/admin/tags/{id}"
 *          }
 *      }
 * )
 * @ORM\Entity(repositoryClass=TagRepository::class)
 * @UniqueEntity("libelle",message="Le libelle est unique")
 */
class Tag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"tag:read","tag:write","groupeTag:read","tagInGrpeTag:read","groupeTag:write"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ du libelleTag est obligatoire ")
     * @Groups({"tag:read","tag:write","groupeTag:read","tagInGrpeTag:read","groupeTag:write"})
     */
    private $libelle;

    /**
     * @ORM\ManyToMany(targetEntity=GroupeTag::class, mappedBy="tags")
     * @Assert\NotBlank(message="Le champ du groupeTag est obligatoire")
     * @Groups({"tag:read","tag:write"})
     */
    private $groupeTags;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"tag:read"})
     */
    private $isDeleted;

    public function __construct()
    {
        $this->groupeTags = new ArrayCollection();
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
     * @return Collection|GroupeTag[]
     */
    public function getGroupeTags(): Collection
    {
        return $this->groupeTags;
    }

    public function addGroupeTag(GroupeTag $groupeTag): self
    {
        if (!$this->groupeTags->contains($groupeTag)) {
            $this->groupeTags[] = $groupeTag;
            $groupeTag->addTag($this);
        }

        return $this;
    }

    public function removeGroupeTag(GroupeTag $groupeTag): self
    {
        if ($this->groupeTags->removeElement($groupeTag)) {
            $groupeTag->removeTag($this);
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
