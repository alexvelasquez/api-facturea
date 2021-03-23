<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class MovimientoRepository extends EntityRepository
{
    public function montosPorFecha($fecha){
        $em = $this->getEntityManager();
        $dql = "SELECT COALESCE(SUM(m.valor),0) as total
                FROM App:Movimiento as m
                INNER JOIN m.tipoMovimiento as tm
                WHERE m.fCreacion >= :fecha AND tm.codigo = :codigo";
        $query = $em->createQuery($dql)
        ->setParameter(':fecha',$fecha)
        ->setParameter(':codigo','DECREMENTO');
        return $query->getSingleResult();
    }

}