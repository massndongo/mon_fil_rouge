<?php

namespace App\DataPersister;

use App\Entity\GroupeCompetence;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class GroupeCompetenceDataPersister implements ContextAwareDataPersisterInterface
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
        return $data instanceof GroupeCompetence;
    }

    /**
     * @param GroupeCompetence $data
     */
    public function persist($data, array $context = [])
    {
        dd($context);
        $this->_entityManager->persist($data);
        $this->_entityManager->flush();
       return $data;
    }


    /**
     * {@inheritdoc}
     */
    
    public function remove($data, array $context = [])
    {
        $archive = $data->setIsDeleted(true);
        $this->_entityManager-> persist($archive);
        $this->_entityManager->flush();
        return $this->json($archive, Response::HTTP_OK);
    }
}
