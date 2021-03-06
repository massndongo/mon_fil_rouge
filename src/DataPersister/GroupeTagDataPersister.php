<?php

namespace App\DataPersister;

use App\Entity\GroupeTag;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

/**
 *
 */

class GroupeTagDataPersister implements ContextAwareDataPersisterInterface
{
   
    public function __construct(EntityManagerInterface $entityManager){

        $this->_entityManager = $entityManager;
    }
    /**
     * {@inheritdoc}
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof GroupeTag;
    }

    /**
     * @param GroupeTag $data
     */
    public function persist($data, array $context = [])
    {
        
        $this->_entityManager->persist($data);
        $this->_entityManager->flush();
    }


    /**
     * {@inheritdoc}
     */
    
    public function remove($data, array $context = [])
    {
        $data->setIsDeleted(true);
       // $this->_entityManager->remove($data);
        $this->_entityManager->flush();
        $this->json($data,Response::HTTP_OK);
    }
}