<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="discriminator", type="string")
 * @ORM\DiscriminatorMap({"user" = "User", "admin" = "Admin", "formateur" = "Formateur", "apprenant" = "Apprenant","cm"="Cm"})
 * @ApiFilter(BooleanFilter::class, properties={"isDeleted"})
 * @ApiFilter(SearchFilter::class, properties={"profil": "exact"})
 * @ApiResource(
 *      routePrefix="/admin",
 *      normalizationContext={"groups"={"user:read"}},
 *      denormalizationContext={"groups"={"user"}},
 *      collectionOperations={
*          "create_user"={
*              "method"="POST",
*              "path"="/users",
*              "security"="is_granted('ROLE_ADMIN')",
*              "security_message"="Vous n'avez pas access à cette Ressource"
 *          },
 *          "get_users"={
 *              "method"="GET",
 *              "path"="/users",
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"="Vous n'avez pas access à cette Ressource"
 *          }
 *      },
 *      itemOperations={
 *          "delete_user"={
*              "method"="DELETE",
*              "path"="/users/{id}",
*              "security"="is_granted('ROLE_ADMIN')",
*              "security_message"="Vous n'avez pas access à cette Ressource"   
 *          },
 *          "update_user"={
 *              "deserialize"=false,
*              "method"="PUT",
*              "path"="/users/{id}",
*              "security"="is_granted('ROLE_ADMIN')",
*              "security_message"="Vous n'avez pas access à cette Ressource"   
 *          },
 *          "get_user"={
 *              "method"="GET",
 *              "path"="/users/{id}",
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"="Vous n'avez pas access à cette Ressource"
 *          }
 *      }      
 * )
 * 
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user","user:read","profilUsers:read"})
     * @Assert\NotBlank(message="Le nom est obligatoire")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Groups({"user","user:read","profilUsers:read"})
     * @Assert\NotBlank(message="L'email est obligatoire")
     * @Assert\Email(
     *     message="Veuillez saisir un email valide."
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user","user:read","profilUsers:read"})
     * @Assert\NotBlank(message="Le prenom est obligatoire")
     */
    private $prenom;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=255)
     * @Groups({"user"})
     * @Assert\NotBlank(message="Le password est obligatoire")
     */
    protected $password;

    /**
     * @ORM\JoinColumn(nullable=false)
     * @ORM\ManyToOne(targetEntity="Profil", inversedBy="users",cascade={"persist"})
     * @ApiSubresource()
     * @Groups({"user","user:read"})
     */
    protected $profil;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank(message="Le username est obligatoire")
     * @Groups({"user","user:read"})
     */
    protected $username;

    /**
     * @ORM\Column(type="boolean")
     * @Groups({"user"})
     */
    private $isDeleted=false;

    /**
     * @ORM\Column(type="blob")
     * @Groups({"user"})
     */
    private $avatar;

    public function getId(): ?int
    {
        return $this->id;
    }
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        return ["ROLE_".$this->profil->getLibelle()];
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }
        /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password; //$encoder->encodePassword($this,$password);

        return $this;
    }

    public function getProfil(): ?profil
    {
        return $this->profil;
    }

    public function setProfil(?profil $profil): self
    {
        $this->profil = $profil;

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

    public function getAvatar()
    {
        return base64_encode(stream_get_contents($this->avatar));
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }
}
