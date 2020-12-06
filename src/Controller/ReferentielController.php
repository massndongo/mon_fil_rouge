<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\ValidatorInterface;
use App\Entity\Referentiel;
use App\Repository\GroupeCompetenceRepository;
use App\Repository\ReferentielRepository;
use App\Service\UserServices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ReferentielController extends AbstractController
{
    private const ACCESS_DENIED = "Vous n'avez pas accés à cette ressource.",
                    REFERENTIEL_READ = "ref:red",
                    RESOURCE_NOT_FOUND = "Ressource inexistante.";
    public function __construct (EntityManagerInterface $manager, GroupeCompetenceRepository $repoGroupeComp, SerializerInterface $serializer,ReferentielRepository $referentielRepository)
    {
        $this->serializer = $serializer;
        $this->manager = $manager;
        $this->repoGroupeComp = $repoGroupeComp;
        $this->referentielRepository = $referentielRepository;
    }
    /**
     * @Route(
     *     path="/api/admin/referentiels",
     *     methods={"POST"}
     * )
    */
    public function addReferentiels(ValidatorInterface $validator, UserServices $userservices, Request $request, EntityManagerInterface $manager)
    {
        if(!($this->isGranted("EIDT",new Referentiel())))
        {
            return $this->json(["message" => self::ACCESS_DENIED],Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();
        $key = 'programme';   
        $data[$key] = $userservices->getAvatar($request,$key);
        $ref = $this->serializer->denormalize($data, "App\Entity\Referentiel", 'json');
        $errors = $validator->validate($ref);
        if($errors)
        {
            return $this->json($errors,Response::HTTP_BAD_REQUEST);
        }
        $groupeComp = $data['groupeCompetences'];
        if (count($groupeComp)) {
            foreach ($groupeComp as $groupe) {
                $grpe = $this->repoGroupeComp->find($groupe);
                $ref->addGroupeCompetence($grpe);
            }
        }else {
            return $this->json(["message" => "Impossibe d'ajouter un groupe de compétences non existant."],Response::HTTP_BAD_REQUEST);
        }
        $manager->persist($ref);
        $manager->flush();
        return $this->json($ref,Response::HTTP_CREATED);
    }
    /**
     * @Route(
     *     path="/api/admin/referentiels/{id}",
     *     methods={"PUT"}
     * )
    */
    public function setReferentiel(UserServices $userservices, Request $request, $id){
        if(!($this->isGranted("SET",new Referentiel())))
        {
            return $this->json(["message" => self::ACCESS_DENIED],Response::HTTP_FORBIDDEN);
        }
        $data = $request->request->all();
        $ref = $this->referentielRepository->findOneBy(["id"=>$id]);
        if ($data["libelle"]) {
            $ref->setLibelle($data["libelle"]);
        }
        if ($data["presentation"]) {
            $ref->setPresentation($data["presentation"]);
        }
        if ($data["critereEvaluation"]) {
            $ref->setCritereEvaluation($data["critereEvaluation"]);
        }
        if ($data["critereAdmission"]) {
            $ref->setCritereAdmission($data["critereAdmission"]);
        }

        if ($data["programme"]) {
            $key = "programme";
            $data[$key] = $userservices->getAvatar($request,$key);
            $ref->setProgramme($data[$key]);
        }
        if ($data["groupeCompetences"]) {
            foreach ($data["groupeCompetences"] as $key => $groupe) {
                $grpe = $this->repoGroupeComp->find($groupe);
                if ($grpe && !$grpe->getIsDeleted()) {
                    if ($key=="del") {
                        $ref->removeGroupeCompetence($grpe);
                    }
                    if ($key=="add") {
                        $ref->addGroupeCompetence($grpe);
                    }
                }
            }
        }
        dd($ref);
        $this->manager->persist($ref);
        $this->manager->flush();
        return $this->json($ref,Response::HTTP_CREATED);
    }
    /**
     * @Route(
     *     path="/api/admin/referentiels",
     *     methods={"GET"},
     *     name="getReferentiels"
     * )
     */
    public function getReferentiels()
    {
        if(!($this->isGranted("VIEW",new Referentiel())))
        {
            return $this->json(["message" => self::ACCESS_DENIED],Response::HTTP_FORBIDDEN);
        }
        $referentiels = $this->referentielRepository->findBy(["isDeleted" => false]);
        return $this->json($referentiels,Response::HTTP_OK);
    }
    /**
     * @Route(
     *     path="/api/admin/referentiels/{idR}/grpecompetences/{id}",
     *     methods={"GET"},
     *     name="getCompetenceInGrpeCompInReferentiels"
     * )
     */
    public function getCompetenceInGrpeCompInReferentiels($idR, $id)
    {
        if(!($this->isGranted("VIEW",new Referentiel())))
        {
            return $this->json(["message" => self::ACCESS_DENIED],Response::HTTP_FORBIDDEN);
        }
        $referentiels = $this->referentielRepository->findOneBy(["id" => $idR]);
        if($referentiels && !$referentiels->getIsDeleted())
        {
            $grpe = $this->repoGroupeComp->findOneBy(["id" => (int)$id]);
            $groupC = $referentiels->getGroupeCompetence();
            foreach ($groupC as $groupe){
                if ($grpe && $id == $grpe->getId()) {
                    $competences = $grpe->getCompetences();
                }
            }
            return $this->json($competences,Response::HTTP_OK);
        }
        return  $this->json(["message" => self::RESOURCE_NOT_FOUND],Response::HTTP_NOT_FOUND);
    }

}
