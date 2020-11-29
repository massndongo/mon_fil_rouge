<?php

namespace App\Controller;

use App\Entity\ProfilSortie;
use App\Repository\PromoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilSortieController extends AbstractController
{

    private const ACCESS_DENIED = "Vous n'avez pas access à cette Ressource",
                    RESOURCE_NOT_FOUND = "Ressource inexistante",
                    PROMO_APPRENANT_READ = "promos:appreant:read",
                    PROMO_READ = "promos:read";
    public function __construct(ValidatorInterface $validator,EntityManagerInterface $manager,SerializerInterface $serializer,PromoRepository $promoRepository)
    {
        $this->serializer = $serializer;
        $this->promoRepository = $promoRepository;
        $this->manager = $manager;
        $this->validator = $validator;
    }

    /**
     * @Route(
     *     path="/api/admin/promo/{id}/profilsorties",
     *     methods={"GET"},
     *     name="getStudentInPromoByProfilSortie"
     * )
     */
    public function getStudentInPromoByProfilSortie($id)
    {
        if (!$this->isGranted("VIEW",new ProfilSortie()))
        {
            return $this->json(["message" => self::ACCESS_DENIED],Response::HTTP_FORBIDDEN);
        }
        $promo = $this->promoRepository->find($id);
        $promoTab = [];
        if ($promo && !$promo->getIsDeleted())
        {
            $groupes = $promo->getGroupes();
            foreach ($groupes as $groupe) {
                $promoTab["apprenant"] = $groupe->getApprenant();
                foreach ($promoTab["apprenant"] as $apprenant) {
                    if(!($apprenant->getProfilSortie())){
                        return $this->json(["message" => "Ce profil n existe pas."],Response::HTTP_NOT_FOUND);
                    }
                    $students = $apprenant->getProfilSortie()->getApprenant();
                    $student[] = $students;
                }
            }

            dd($student);
        $promoTab = $this->serializer->normalize($promoTab,null,["groups" => [self::PROMO_READ,self::PROMO_APPRENANT_READ]]);
        return $this->json($promoTab,Response::HTTP_OK);
        }

return $this->json(["message" => self::RESOURCE_NOT_FOUND],Response::HTTP_NOT_FOUND);
    }
    /**
     * @Route(
     *     path="/api/admin/promo/{id}/profilsorties/{idP}",
     *     methods={"GET"},
     *     name="getStudentProfilSortieInPromo"
     * )
     */
    public function getStudentProfilSortieInPromo($id)
    {
        if (!$this->isGranted("VIEW",new ProfilSortie()))
        {
            return $this->json(["message" => self::ACCESS_DENIED],Response::HTTP_FORBIDDEN);
        }
        $promo = $this->promoRepository->findOneBy(["id" => $id]);
        if ($promo && !$promo->getIsDeleted())
        {
            $promoTab = [];
            $groupes = $promo->getGroupes();
            foreach ($groupes as $groupe) {
                $promoTab["apprenant"][] = $groupe->getApprenant();
                foreach ($promoTab["apprenant"] as $apprenant) {
                    if(!($apprenant->getProfilSortie())){
                        return $this->json(["message" => "Ce profil n existe pas."],Response::HTTP_NOT_FOUND);
                    }
                    $students = $apprenant->getProfilSortie();
                    $student = $students->getApprenants();
                    return  $this->json($student,Response::HTTP_OK);
                }
            }
        return $this->json(["message" => self::RESOURCE_NOT_FOUND],Response::HTTP_NOT_FOUND);
        }
    }
}
