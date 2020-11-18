<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilController extends AbstractController
{
    private const ACCESS_DENIED = "Vous n'avez pas accés à cette ressource.",
        RESOURCE_NOT_FOUND = "Ressource inexistante.",
        PROFIL_READ = "profil:read",
        PROFIL_USERS = "profilUsers:read";
    public function __construct(ProfilRepository $profilRepository,SerializerInterface $serializer)
    {
        $this->profilRepository = $profilRepository;
        $this->serializer = $serializer;
    }
    /**
     * @Route(
     *     path="/api/admin/profils",
     *     methods={"GET"},
     *     name="get_profils",
     *     defaults={
     *          "_api_receive"=false,
     *     }
     * )
     */
    public function getProfils(Request $request)
    {
        $profils = $this->profilRepository->findBy([
            "isDeleted" => false
        ]);
        $profils = $this->serializer->normalize($profils,null);
        return $this->json($profils,Response::HTTP_OK);
    }
    /* @Route(
        *     path="/api/admins/profils/{id<\d+>}/users",
        *     methods={"GET"},
        * )
        */
       public function getUsersInProfil($id,ProfilRepository $profilRepository)
       {
           $profil = $profilRepository->findOneBy(["id" => $id]);
           if($profil && !$profil->getIsDeleted()){
               $profil = $this->serializer->normalize($profil,null,["groups" => [self::PROFIL_USERS]]);
               return $this->json($profil,Response::HTTP_OK);
           }
           return $this->json(["message" => self::RESOURCE_NOT_FOUND],Response::HTTP_NOT_FOUND);
       }
}