<?php

namespace App\Entity;

use App\Repository\CmRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity(repositoryClass=CmRepository::class)
 * @ApiResource(
 *      attributes={
 *          "security"="is_granted('ROLE_CM')",
 *          "security_message"="Vous n'avez pas access à cette Ressource"
 *      },
 *      collectionOperations={
*          "create_formateur"={
*              "method"="POST",
*              "path"="/api/formateurs"
 *          },
 *          "get_apprenants"={
 *              "method"="GET",
 *              "path"="/api/apprenants"
 *          }
 *      },
 *      itemOperations={
 *          "update_formateur"={
 *              "deserialize"=false,
*              "method"="PUT",
*              "path"="/api/formateurs{id}"  
 *          },
 *          "get_apprenant"={
 *              "method"="GET",
 *              "path"="/api/apprenants/{id}"
 *          }
 *      }      
 * )
 */
class Cm extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
