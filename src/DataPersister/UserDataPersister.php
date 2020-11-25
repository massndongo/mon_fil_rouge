<?php
// src/DataPersister/UserDataPersister.php

namespace App\DataPersister;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

/**
 *
 */

class UserDataPersister implements ContextAwareDataPersisterInterface
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
        return $data instanceof User;
    }

    /**
     * @param User $data
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
        $data->setIsDeleted(true);
        //$this->_entityManager->remove($data);
        $this->_entityManager->flush();
    }
}