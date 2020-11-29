<?php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use App\Entity\GroupeCompetence;
use App\Repository\CompetenceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\GroupeCompetenceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Core\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GroupeCompetenceController extends AbstractController
{
    private $groupeCompetenceRepository,
    $serializer;
    private const ACCESS_DENIED = "Vous n'avez pas access à cette Ressource",
        RESOURCE_NOT_FOUND = "Ressource inexistante",
        GROUPE_COMPETENCE_READ = "grpecompetence:read_m",
        COMPETENCE_READ = "grpecompetence:competence:read";

    public function __construct(EntityManagerInterface $manager, GroupeCompetenceRepository $groupeCompetenceRepository,SerializerInterface $serializer)
    {
    $this->groupeCompetenceRepository = $groupeCompetenceRepository;
    $this->serializer = $serializer;
    $this->manager = $manager;
    }
   /**
     * @Route(
     *     path="/api/admin/grpecompetences",
     *     methods={"POST"},
     *     name="createGroupeCompetence"
     * )
     */
    public function createGroupeCompetence(DenormalizerInterface $denormalize, CompetenceRepository $competenceRepository,TokenStorageInterface $tokenStorage,Request $request,EntityManagerInterface $manager,SerializerInterface $serializer,ValidatorInterface $validator)
    {
        $groupeCompetenceJson = $request->getContent();
        $administrateur = $tokenStorage->getToken()->getUser();
        $groupeCompetenceTab = $serializer->decode($groupeCompetenceJson,"json");
        $competences = $groupeCompetenceTab["competences"];
        $groupeCompetenceTab["competences"] = [];
        $groupeCompetenceObj = $denormalize->denormalize($groupeCompetenceTab,"App\Entity\GroupeCompetence");
        $groupeCompetenceObj->setIsDeleted(false)
                            ->setAdministrateur($administrateur);
        $groupeCompetenceObj = $this->addComptenceToGroupe($competences,$serializer,$validator,$groupeCompetenceObj,$manager,$competenceRepository);
        $errors = (array)$validator->validate($groupeCompetenceObj);
        if(count($errors))
            {return $this->json($errors,Response::HTTP_BAD_REQUEST);}
        if (!count($competences))
            {return $this->json(["message" => "Ajoutez au moins une competence à cet groupe de competence."],Response::HTTP_BAD_REQUEST);}
        $this->manager->persist($groupeCompetenceObj);
        $this->manager->flush();
        return $this->json($groupeCompetenceObj,Response::HTTP_CREATED);
    }
    private function addComptenceToGroupe($competences,$serializer,$validator,$groupeCompetenceObj,$manager,$competenceRepository)
    {
        foreach ($competences as $comptence){
            $comptence["niveaux"] = [];
            $skill = $serializer->denormalize($comptence,"App\Entity\Competence");
            $id = isset($comptence["id"]) ? (int)$comptence["id"] : null;
            if($id)
            {
                $skill = $competenceRepository->findOneBy(["id" => $id]);
                if(!$skill)
                    {return $this->json($skill,Response::HTTP_NOT_FOUND,["message" => "La competence avec l'id : $id, n'existe pas."]);}
                }else{
                $skill->setIsDeleted(false);
                $error = (array) $validator->validate($skill);
                if (count($error))
                    {return $this->json($error,Response::HTTP_BAD_REQUEST);}
                $this->manager->persist($skill);
                $this->groupeCompetenceObj->addCompetence($skill);
                }
            }
        return $groupeCompetenceObj;
    }
}