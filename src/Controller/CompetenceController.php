<?php

namespace App\Controller;

use App\Entity\Competence;
use App\Service\CompetenceServices;
use App\Repository\NiveauRepository;
use App\Repository\CompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GroupeCompetenceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonDecode;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CompetenceController extends AbstractController
{
    private const ACCESS_DENIED = "Vous n'avez pas accés à cette ressource.",
    RESOURCE_NOT_FOUND = "Ressource inexistante.",
    COMPETENCE_READ = "competence:read";

    private $competenceRepository,$serializer;
    public function __construct(CompetenceServices $competenceServices,NiveauRepository $niveauRepo,CompetenceRepository $competenceRepository, DenormalizerInterface $denormalize ,SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
        $this->denormalize = $denormalize;
        $this->competenceRepository = $competenceRepository;
        $this->niveauRepo = $niveauRepo;
        $this->competenceServices = $competenceServices;
    }
  /**
     * @Route(
     *     path="/api/admin/competences",
     *     methods={"POST"},
     *     name="create_competences"
     * )
     */
    public function createCompetences(SerializerInterface $serializer,CompetenceServices $competenceServices, NormalizerInterface $normalize ,TokenStorageInterface $tokenStorage,Request $request,EntityManagerInterface $manager,ValidatorInterface  $validator,GroupeCompetenceRepository $groupeCompetenceRepository)
    {
        $competenceJson = $request->getContent();
        $administrateur = $tokenStorage->getToken()->getUser();
        $competenceTab = $serializer->decode($competenceJson,"json");
        $groupeCompetences = isset($competenceTab["groupeCompetences"]) ? $competenceTab["groupeCompetences"] : [];
        $niveaux = isset($competenceTab["niveaux"]) ? $competenceTab["niveaux"] : [];
        $competenceTab["groupeCompetences"] = [];
        $competenceTab["niveaux"] = [];
        $competenceObj = $this->denormalize->denormalize($competenceTab,"App\Entity\Competence");
        $competenceObj->setIsDeleted(false);
        $errors = $validator->validate($competenceObj);
        if (count($errors)){
            return $this->json($errors,Response::HTTP_BAD_REQUEST);
        }
        if(!count($competenceTab)){
            return $this->json(["message" => "Ajouter au moins un groupe de competence"],Response::HTTP_BAD_REQUEST);
        }
        if(!count($niveaux) || count($niveaux) < 3){
            return $this->json(["message" => "Ajouter les 3 niveaux d'évaluation."],Response::HTTP_BAD_REQUEST);
        }
        $competenceObj = $this->competenceServices->addGroupeToCompetence($groupeCompetences,$groupeCompetenceRepository,$competenceObj);

        foreach ($niveaux as $niveau)
        {
            $level = $this->denormalize->denormalize($niveau,"App\Entity\Niveau");
            $level->setIsDeleted(false);
            $error = $validator->validate($level);
            if(count($error))
            {
                return $this->json($error,Response::HTTP_BAD_REQUEST);
            }
            $manager->persist($level);
            $competenceObj->addNiveau($level);
        }
        $manager->persist($competenceObj);
        $manager->flush();
        $competenceObj = $normalize->normalize($competenceObj,null,["groups" => [self::COMPETENCE_READ]]);
        return $this->json($competenceObj,Response::HTTP_CREATED);
    }
}
