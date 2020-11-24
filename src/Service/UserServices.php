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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserServices{

   private $serializer;
   private $encoder;
   private $profilRepo;

     public function __construct(DenormalizerInterface $denormalizer, SerializerInterface $serializer ,UserPasswordEncoderInterface $encoder, ProfilRepository $profilRepo){
	  $this->serializer = $serializer;
	  $this->denormalize = $denormalizer;
      $this->encoder = $encoder;
      $this->repo = $profilRepo;
     }

    public function addUser(Request $request,$manager){
      
	  $data = $request->request->all();
	  $prf = $data["profil"];
	  $profile = $this->repo->findOneByLibelle($prf);
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
	  dd($data);

      $user = $this->denormalize->denormalize($data, $userType, 'json');

      $user->setProfil($prf);

      $password = $user->getPassword();
      $user->setPassword($this->encoder->encodePassword($user,$password));

      $manager->persist($user);
      $manager->flush();

      return $user;

	}
}