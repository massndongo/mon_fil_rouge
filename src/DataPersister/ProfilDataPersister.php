<?php

namespace App\DataPersister;

use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;
use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;

class ProfilDataPersister implements ContextAwareDataPersisterInterface
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
        return $data instanceof Profil;
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
        $archive = $data->setIsDeleted(true);
        $this->_entityManager-> persist($archive);
        $users = $data->getUsers();
        foreach ($users as $user) {
            $archiveUser = $user->setIsDeleted(true);
            $this->_entityManager-> persist($archiveUser);
        }
        $this->_entityManager->flush();
    }
}
