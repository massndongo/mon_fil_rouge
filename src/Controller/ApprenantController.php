<?php

namespace App\Controller;

use App\Repository\ProfilRepository;
use App\Repository\UserRepository;
use App\Service\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApprenantController extends AbstractController
{
    public function __construct(UserServices $userService, EntityManagerInterface $manager, ProfilRepository $repo){
        $this->userService = $userService;
        $this->manager = $manager;
        $this->repo = $repo;
    }
    /**
     * @Route(
     *     path="/api/apprenants",
     *     methods={"POST"}
     * )
    */
    public function addApprenants(UserServices $userService,Request $request)
    {
        $todo="create";
        $user = $this->userService->addUser($request,$todo);
        $this->manager->persist($user);
        $this->manager->flush();
        return $this->json($user,Response::HTTP_CREATED);
    }
    /**
     * @Route(
     * path="/api/apprenants/{id}",
     * methods={"PUT"}
     * )
    */
    public function updateUsers(UserServices $userService,Request $request,$id)
    {
        $todo = $id;
        $user = $this->userService->addUser($request,$todo);
        $this->manager->persist($user);
        $this->manager->flush();
        return $this->json($user,Response::HTTP_OK);
    }
}
