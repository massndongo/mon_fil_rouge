<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Apprenant;
use App\Service\UserServices;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    public function __construct(UserServices $userService, EntityManagerInterface $manager){
        $this->userService = $userService;
        $this->manager = $manager;
    }
    /**
     * @Route(
     *     path="/api/admin/users",
     *     methods={"POST"}
     * )
    */
    public function addUsers(UserServices $userService,Request $request)
    {
        $todo="create";
        $this->userService->addUser($request,$todo);
        
        return $this->json($userService,Response::HTTP_OK);
    }
    /**
     * @Route(
     * path="/api/admin/users/{id}",
     * methods={"PUT"}
     * )
    */
    public function updateUsers(UserServices $userService,Request $request,$id)
    {
        $todo = $id;
        $this->userService->addUser($request,$todo);
        $this->manager->flush();
        return $this->json($userService,Response::HTTP_OK);
    }
    /**
     * @Route(
     *     path="/api/admin/users/{id}",
     *     methods={"DELETE"},
     *     defaults={
     *          "__controller"="App\Controller\UserController::DeleteUser",
     *          "__api_resource_class"=User::class,
     *          "__api_collection_operation_name"="delete_user"
     *         }
     * )
    */
    public function DeleteUser(Request $request,SerializerInterface $serializer,ValidatorInterface $validator,UserRepository $userRep,EntityManagerInterface $manager,$id)
    {
        $user= $userRep->find($id);
        $user->setisDeleted(true);
        $manager->persist($user);
        $manager->flush();
        return $this->json($user,Response::HTTP_OK);
    }
}
