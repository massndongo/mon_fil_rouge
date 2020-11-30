<?php

namespace App\DataFixtures;

use App\Entity\ProfilSortie;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProfilSortieFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $tabProfil=["Developpeur Front", "Developpeur Back", "Developpeur Fullstack"];

        foreach ($tabProfil as $profil) {
           
            $prf= new ProfilSortie();
            $prf->setLibelle($profil)
                ->setIsDeleted(false);
           $manager->persist($prf);
        } 
        // $product = new Product();
        

        $manager->flush();
    }
}