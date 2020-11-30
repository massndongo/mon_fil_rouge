<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Competence;
use App\Repository\NiveauRepository;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use App\Repository\GroupeCompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;

class CompetenceFixtures extends Fixture
{
    private $niveauRepo,
            $grpeCompetenceRepo,
            $manager;

    public function __construct(EntityManagerInterface $manager, NiveauRepository $niveauRepo, GroupeCompetenceRepository $grpeCompetenceRepo)
    {
        $this->manager = $manager;
        $this->niveauRepo = $niveauRepo;
        $this->grpeCompetenceRepo = $grpeCompetenceRepo;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $competence = new Competence();
        $times = 3;
        for ($i=1; $i <= $times; $i++) {
            $grpeCompetence = $this->grpeCompetenceRepo->findOneBy(["id" => $i]);
            $niveau = $this->niveauRepo->findOneBy(["id" => $i]);
            $competence->addNiveau($niveau);
            $competence->addGroupeCompetence($grpeCompetence);
            $competence->setLibelle($faker->title()) 
                    ->setDescriptif($faker->text())
                    ->setIsDeleted(false);
            $this->manager->persist($competence); 
        }
        $this->manager->flush();
    }
}
