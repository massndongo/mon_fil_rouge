<?php

namespace App\Controller;

use App\Service\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FormateurController extends AbstractController
{
    public function __construct(UserServices $userService, EntityManagerInterface $manager){
        $this->userService = $userService;
        $this->manager = $manager;
    }
    /**
     * @Route(
     *     path="/api/formateurs",
     *     methods={"POST"}
     * )
    */
    public function addFormateurs(Request $request)
    {
        $todo="create";
        $user = $this->userService->addUser($request,$todo);
        $this->manager->persist($user);
        $this->manager->flush();
        return $this->json($user,Response::HTTP_CREATED);
    }
    /**
     * @Route(
     * path="/api/formateurs/{id}",
     * methods={"PUT"}
     * )
    */
    public function updateFormateurs(Request $request,$id)
    {
        $todo = $id;
        $user = $this->userService->addUser($request,$todo);
        $this->manager->persist($user);
        $this->manager->flush();
        return $this->json($user,Response::HTTP_OK);
    }
}
