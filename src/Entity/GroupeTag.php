<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupeTagRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(attributes={
 *     "normalization_context"={"groups"={"groupeTag:read"}},
 *     "denormalization_context"={"groups"={"groupeTag:write"}}},
 *      collectionOperations={
*          "create_grpeTags"={
*              "method"="POST",
*              "path"="/admin/grptags"
            },
 *          "get_all_grpeTags"={
 *             "normalization_context"={"groups"={"tagInGrpeTag:read"}},
 *              "method"="GET",
 *              "path"="/admin/grptags"
 *          }
 *      },
 *      itemOperations={
 *          "get_grpeTag"={
 *              "method"="GET",
 *              "path"="/admin/grptags/{id}"
 *          },
 *          "get_Tags_In_grpeTag"={
 *              "method"="GET",
 *              "path"="/admin/grptags/{id}/tags"
 *          },
 *          "set_grpeTag"={
 *              "method"="PUT",
 *              "path"="/admin/grptags/{id}"
 *          },
 *          "delete_grpeTag"={
 *              "method"="DELETE",
 *              "path"="/admin/grptags/{id}"
 *          }
 *      }
 * )
 * @ORM\Entity(repositoryClass=GroupeTagRepository::class)
 * @UniqueEntity("libelle",message="Le champ du libelleGrpeTag est unique")
 */
class GroupeTag
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"tag:read","tag:write","groupeTag:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le champ libelleGrpeTag est obligatoire")
     * @Groups({"tag:read","tag:write","groupeTag:read","groupeTag:write"})
     */
    private $libelle;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="groupeTags")
     * @Assert\NotBlank(message="Le champ du Tag est obligatoire")
     * @Groups({"groupeTag:read","tagInGrpeTag:read","groupeTag:write"})
     */
    private $tags;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"tag:read","groupeTag:read"})
     */
    private $isDeleted;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

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
