<?php

namespace App\Service;

use App\Entity\Cm;
use App\Entity\Admin;
use App\Entity\Apprenant;
use App\Entity\Formateur;
use App\Repository\UserRepository;
use App\Repository\ProfilRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserServices{

   private $serializer;
   private $encoder;
   private $profilRepo;
   private $validator;
   private $manager;

     public function __construct(UserRepository $userRepo, EntityManagerInterface $manager, ValidatorInterface $validator, DenormalizerInterface $denormalizer, SerializerInterface $serializer ,UserPasswordEncoderInterface $encoder, ProfilRepository $profilRepo){
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->manager = $manager;
        $this->denormalize = $denormalizer;
        $this->encoder = $encoder;
        $this->repo = $profilRepo;
        $this->userRepo = $userRepo;
     }
     public function getUserType($profil)
     {
         switch ($profil->getLibelle())
         {
             case "ADMIN": return "App\Entity\User";
             case "FORMATEUR": return "App\Entity\Formateur";
             case "APPRENANT": return "App\Entity\Apprenant";
             case "CM": return "App\Entity\CM";
         }
     }
     public function getAvatar(Request $request,$key){
      $uploadedFile = $request->files->get($key);

      if($uploadedFile){
        $file = $uploadedFile->getRealPath();
        $avatar = fopen($file, 'r+');
        $data[$key] = $avatar;
        return $data[$key];
      }
     }

    public function addUser(Request $request,$todo){
      if ($todo=="create") {
        $key = 'avatar';   
        $data = $request->request->all();
        $prf = $data["role"];
        $profile = $this->repo->findOneBy(["libelle"=>$prf]);
        $data['avatar'] = $this->getAvatar($request, $key);
          $userType = $this->getUserType($profile);
          $user = $this->denormalize->denormalize($data, $userType, 'json');
          $user->setIsDeleted(false);
          $user->setProfil($profile);
          $password = $user->getPassword();                                                                                                                   
          $user->setPassword($this->encoder->encodePassword($user,$password));

          return $user;

	  }else {
      $data = $request->request->all();
      if (!$data) {
        $data = json_decode($request->getContent(), true);
        
      }
      //return $data;
      $user = $this->userRepo->findOneBy(["id"=>$todo]);
      $key = 'avatar';
      if ($data["username"]) {
        $user->setUsername($data["username"]);
      }
      if($data["password"]){
        $password = $user->getPassword();
        $user->setPassword($this->encoder->encodePassword($user, $password));
      }
      $data[$key] = $this->getAvatar($request,$key);
        if($data[$key]){
          $user->setAvatar($data[$key]);
        }
        if ($data["nom"]) {
          $user->setNom($data["nom"]);
        }
        if ($data["prenom"]) {
          $user->setPrenom($data["prenom"]);
        }
        if ($data["email"]) {
          $user->setEmail($data["email"]);
        }
        return $user;
    }
  }
}