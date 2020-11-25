<?php

namespace App\Controller;

use App\Entity\Profil;
use App\Repository\ProfilRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProfilController extends AbstractController
{
    private $profilRepository,
    $serializer;

    private const ACCESS_DENIED = "Vous n'avez pas accés à cette ressource.",
    RESOURCE_NOT_FOUND = "Ressource inexistante.",
    PROFIL_READ = "profil:read",
    PROFIL_USERS = "profilUsers:read";

    public function __construct(ProfilRepository $profilRepository,NormalizerInterface $serializer)
    {
    $this->profilRepository = $profilRepository;
    $this->serializer = $serializer;
    }
/**
     * @Route(
     *     path="/api/admin/profils/{id<\d+>}/users",
     *     methods={"GET"},
     * )
     */
    public function getUsersInProfil($id)
    {
        $profil = $this->profilRepository->findOneBy(["id" => $id]);
        if($profil && !$profil->getIsDeleted()){
            $profil = $this->serializer->normalize($profil,null,["groups" => [self::PROFIL_USERS]]);
            return $this->json($profil,Response::HTTP_OK);
        }
        return $this->json(["message" => self::RESOURCE_NOT_FOUND],Response::HTTP_NOT_FOUND);
    }

}