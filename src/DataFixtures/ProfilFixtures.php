<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Profil;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProfilFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
                $profils = [
                    "ADMIN",
                    "APPRENANT",
                    "FORMATEUR",
                    "CM"
                ];
        
            foreach ($profils as $profil){
                $libelle = new Profil();
                $libelle->setLibelle($profil);
                $manager->persist($libelle);
            }
        $manager->flush();
        
    }
}
