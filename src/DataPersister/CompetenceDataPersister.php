<?php

namespace App\DataPersister;

use App\Entity\Competence;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class CompetenceDataPersister implements ContextAwareDataPersisterInterface
{
    private $_entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->_entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Competence;
    }

    /**
     * @param Competence $data
     */
    public function persist($data, array $context = [])
    {
        $this->_entityManager->persist($data);
        $this->_entityManager->flush();

       return $data;
    }


    /**
     * {@inheritdoc}
     */
    
    public function remove($data, array $context = [])
    {
        // $archive = $data->setIsDeleted(true);
        // $this->_entityManager-> persist($archive);
        // $groupes = $data->getGroupeCompetences();
        // foreach ($groupes as $groupe) {
        //     $archiveCompetence = $groupe->setIsDeleted(true);
        //     $this->_entityManager-> persist($archiveCompetence);
        // }
        // $this->_entityManager->flush();
        // return $this->json($archiveCompetence,Response::HTTP_CREATED);
    }
}
