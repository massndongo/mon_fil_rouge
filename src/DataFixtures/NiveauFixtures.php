<?php

namespace App\DataFixtures;

use App\Entity\Niveau;
use App\Entity\Competence;
use Doctrine\Persistence\ObjectManager;
use App\Repository\CompetenceRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;

class NiveauFixtures extends Fixture
{
    private $competenceRepo;
    public function __construct(CompetenceRepository $competenceRepo){
        $this->competenceRepo = $competenceRepo;
    }
    public function load(ObjectManager $manager)
    {
        $competences = $this->competenceRepo->findAll();
        for ($i=1; $i<=3; $i++) {
            foreach ($competences as $competence) {
            $niveau= new Niveau();
            $niveau->setCompetence($competence)
                ->setLibelle("Niveau ".$i)
                ->setCritereEvaluation("Critere Evaluation ".$i)
                ->setIsDeleted(false)
                ->setGroupeAction("Groupe Action ".$i);
           $manager->persist($niveau);
            }
        } 
        // $product = new Product();
        

        $manager->flush();
    }
}