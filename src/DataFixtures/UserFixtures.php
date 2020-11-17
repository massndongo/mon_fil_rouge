<?php

namespace App\DataFixtures;

use App\Entity\Cm;
use Faker\Factory;
use App\Entity\User;
use App\Entity\Admin;
use App\Entity\Profil;
use App\Entity\Apprenant;
use App\Entity\Formateur;
use App\DataFixtures\ProfilFixtures;
use App\Repository\ProfilRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    private $profilRepository;
    private $encoder;
    public function __construct(ProfilRepository $profilRepository,UserPasswordEncoderInterface $encoder)
    {
        $this->profilRepository = $profilRepository;
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $profils = $this->profilRepository->findAll();
        
        $times = 10;
        for ($i=0; $i < $times; $i++) { 
            $password = "";
            $entity = null;
            foreach ($profils as $profil) {
                if ($profil->getLibelle() == "APPRENANT"){
                    $entity = new Apprenant();
                    $password = "apprenant";
                }elseif ($profil->getLibelle() == "FORMATEUR"){
                    $entity = new Formateur();
                    $password = "formateur";
                }elseif ($profil->getLibelle()== "ADMIN"){
                    $entity = new Admin();
                    $password = "admin";
                }elseif($profil->getLibelle()== "CM") {
                    $entity = new Cm();
                    $password = "cm";
                }
                $entity->setPrenom($faker->firstName())
                    ->setUsername($faker->lastName())
                    ->setNom($faker->lastName)
                    ->setIsDeleted(false)
                    ->setPassword($this->encoder->encodePassword($entity,$password))
                    ->setEmail($faker->email)
                    ->setProfil($profil);
                $manager->persist($entity);
                
            }    
        }
        $manager->flush();
    }
    public function getDependencies()
    {
        return array(
            ProfilFixtures::class,
        );
    }
}
