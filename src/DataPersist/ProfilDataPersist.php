<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Profil;
use Doctrine\ORM\EntityManagerInterface;

class ProfilDataPersister implements ContextAwareDataPersisterInterface
{
    private $entityManager;
    public  function __construct(EntityManagerInterface $entityManager){
        $this->entityManager= $entityManager;

    }


    public function supports($data, array $context = []): bool
    {
        // TODO: Implement supports() method.
        return $data instanceof Profil;
    }

    public function persist($data, array $context = [])
    {
        return $data;

    }

    public function remove($data, array $context = [])
    {
        // TODO: Implement remove() method.
        $data->setIsDeleted(true);
        $this->entityManager->persist($data);
        $users= $data->getUsers();
        dd($users);
      foreach ($users as $user){
         $archiveUser = $user->setIsDeleted(true);
          $this->entityManager->persist($archiveUser);
      }
            $this->entityManager->flush();

    }
}