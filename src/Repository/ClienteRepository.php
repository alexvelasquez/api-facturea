<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class ClienteRepository extends EntityRepository
{
    public function montoDebido($cliente){
        $em = $this->getEntityManager();
        $dql = "SELECT SUM(v.montoDebido) as total
                FROM App:Venta as v
                INNER JOIN App:EstadoVenta as ve WITH (ve.venta = v)
                INNER JOIN ve.estado as e
                INNER JOIN v.cliente as c
                WHERE c = :cliente AND ve.vigente = :vigente AND e.codigo = :codigo";
        $query = $em->createQuery($dql)
        ->setParameter(':cliente',$cliente)
        ->setParameter(':vigente','S')
        ->setParameter(':codigo','PENDIENTEPAGO');
        $value = $query->getOneOrNullResult();
        return !empty($value) ? (float)$value['total'] : 0;
    }

    public function movimientos($cliente,$limit){
        $em = $this->getEntityManager();
        $dql = "SELECT m
                FROM App:Cliente as c
                INNER JOIN App:Venta as v WITH (v.cliente = c)
                INNER JOIN App:Movimiento as m WITH (m.venta = v)
                WHERE c = :cliente 
                ORDER BY m.fCreacion DESC";
        $query = $em->createQuery($dql)
        ->setParameter(':cliente',$cliente);
        if(!empty($limit)){
            $query->setMaxResults($limit);
        }
        
        $values = $query->getResult();
        return $values;
    }

}