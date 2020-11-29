<?php

namespace App\Service;

use App\Entity\Competence;
use App\Entity\GroupeCompetence;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\GroupeCompetenceRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class CompetenceServices{

    public function addGroupeToCompetence($groupeCompetences,GroupeCompetenceRepository $groupeCompetenceRepository,Competence $competenceObj)
    {
        foreach ($groupeCompetences as $groupeCompetence)
        {
            $id = isset($groupeCompetence) ? $groupeCompetence : null;
            if ($id)
            {
                $groupe = $groupeCompetenceRepository->findOneBy(["id" => $id]);
                
                if(!$groupe || $groupe->getIsDeleted())
                {
                    return new JsonResponse($groupe,Response::HTTP_NOT_FOUND,["message" => "Ressource inexistante."],true);
                }
                $competenceObj = $competenceObj->addGroupeCompetence($groupe);
            }
        }
        return $competenceObj;
    }
    public function addCompetence(GroupeCompetence $groupeCompetence,$competences)
    {
        foreach ($competences as $competence){
            $groupeCompetence->addCompetence($competence);
        }
        return $groupeCompetence;
    }

}