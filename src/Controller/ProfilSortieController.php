<?php

namespace App\Controller;

use App\Repository\PromoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProfilSortieController extends AbstractController
{
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
    private const ACCESS_DENIED = "Vous n'avez pas access à cette Ressource",
                    RESOURCE_NOT_FOUND = "Ressource inexistante";
    public function getStudentInPromoByProfilSortie($id)
    {
        $promo = $this->promoRepository->findOneBy(["id" => $id]);
        dd($promo);
        if ($promo && !$promo->getIsDeleted())
        {
            $promoTab = [];
            $groupes = $promo->getGroupes();
            foreach ($groupes as $groupe) {
                $promoTab["apprenant"] = $groupe->getApprenant();
            }
            foreach ($promoTab["apprenant"] as $apprenant) {
                if(!($apprenant->getProfilSortie())){
                    return $this->json(["message" => "Ce profil n existe pas."],Response::HTTP_NOT_FOUND);
                }
                return  $this->json($apprenant,Response::HTTP_OK);
            }
        return $this->json(["message" => self::RESOURCE_NOT_FOUND],Response::HTTP_NOT_FOUND);
        }
    }
}
