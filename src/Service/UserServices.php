<?php

namespace App\Service;

use App\Entity\Cm;
use App\Entity\Admin;
use App\Entity\Apprenant;
use App\Entity\Formateur;
use App\Repository\ProfilRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\UserRepository;
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

    public function addUser(Request $request,$todo){
      if ($todo=="create") {
              
        $data = $request->request->all();
        $prf = $data["role"];
        $profile = $this->repo->find($prf);
        $profil = strtoupper(strtolower($profile->getLibelle()));

          $uploadedFile = $request->files->get('avatar');

          if($uploadedFile){
            $file = $uploadedFile->getRealPath();
            $avatar = fopen($file, 'r+');
            $data['avatar'] = $avatar;
          }

          if($profil=='ADMIN'){
            $userType = Admin::class;
          }elseif ($profil=='FORMATEUR') {
            $userType = Formateur::class;
          }elseif ($profil=='CM') {
            $userType = Cm::class;
          }elseif ($profil=='APPRENANT') {
            $userType = Apprenant::class;
        }

          $user = $this->denormalize->denormalize($data, $userType, 'json');
          $user->setIsDeleted(false);
          $user->setProfil($profile);

          $password = $user->getPassword();
          $user->setPassword($this->encoder->encodePassword($user,$password));

          return $user;

	  }else {
      $data = $request->request->all();
      $user = $this->userRepo->findOneBy(["id"=>$todo]);
      if ($data["username"]) {
        $user->setUsername($data["username"]);
      }
      if($data["password"]){
        $password = $user->getPassword();
        $user->setPassword($this->encoder->encodePassword($user, $password));
      }
      $uploadedFile = $request->files->get('avatar');
        if($uploadedFile){
          $file = $uploadedFile->getRealPath();
          $avatar = fopen($file, 'r+');
          $user->setAvatar($avatar);
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
    }
    $this->manager->persist($user);
  }
}