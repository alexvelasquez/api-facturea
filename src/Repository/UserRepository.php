<?php

namespace App\Repository;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository implements UserLoaderInterface
{
    public function loadUserByUsername($usernameOrEmail): ?User
    {
        $entityManager = $this->getEntityManager();

        return $entityManager->createQuery(
            'SELECT u
                FROM App:User u
                WHERE u.username = :query
                OR u.email = :query'
        )
            ->setParameter('query', $usernameOrEmail)
            ->getOneOrNullResult();
    }
    public function findRoleUser()
    {
        $em = $this->getEntityManager();
        $dql = "SELECT u, n
                FROM App:User as u
                INNER JOIN u.negocio as n
                WHERE u.roles like :roles";
        $query = $em->createQuery($dql)
            ->setParameter(':roles', '%[\"ROLE_USER\"]%');
        return $query->getArrayResult();
    }
}
