<?php

namespace App\DataFixtures;

use App\Entity\Profil;
use App\Entity\Referentiel;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;


class ProfilFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        $profil_tab=["ADMIN","FORMATEUR","CM","APPRENANT"];

        foreach ($profil_tab as $lib_profil) {
            
            $profil=new Profil();
            $profil->setLibelle($lib_profil);
            $profil->setIsDeleted(false);
            $manager->persist($profil);

            if ($lib_profil=="ADMIN") {
                $this->setReference("admin",$profil);
            }
            elseif ($lib_profil=="APPRENANT") {
                $this->setReference("apprenant",$profil);
            }  
            elseif ($lib_profil=="FORMATEUR") {
                $this->setReference("formateur",$profil);
            }
            elseif ($lib_profil=="CM") {
                $this->setReference("cm",$profil);
            }
     
        }
        
        

    $manager->flush();
}
}