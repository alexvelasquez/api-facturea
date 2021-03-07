<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function findRoleUser(){
        $em = $this->getEntityManager();
        $dql = "SELECT u, n
                FROM App:User as u
                INNER JOIN u.negocio as n
                WHERE u.roles like :roles";
        $query = $em->createQuery($dql)
        ->setParameter(':roles','%[\"ROLE_USER\"]%');
        return $query->getArrayResult();
    }

}