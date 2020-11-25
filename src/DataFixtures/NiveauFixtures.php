<?php

namespace App\DataFixtures;

use App\Entity\Niveau;
use App\Entity\Competence;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class NiveauFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $competences = $this->competenceRepository->findAll();
        for ($i=1; $i<=3; $i++) {
            foreach ($competences as $competence) {
            $niveau= new Niveau();
            $niveau->setCompetence($competence->getLibelle())
                ->setLibelle("Niveau ".$i)
                ->setCritereEvaluation("Critere Evaluation ".$i)
                ->setGroupeAction("Groupe Action ".$i);
           $manager->persist($niveau);
            }
        } 
        // $product = new Product();
        

        $manager->flush();
    }
}