<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class ComprobanteRepository extends EntityRepository
{
    public function comprobantesPorFechas($negocio,$fechaDesde,$fechaHasta){
        $em = $this->getEntityManager();
        $dql = "SELECT cp,c,v,cv,tc
                FROM App:Comprobante as cp
                INNER JOIN cp.condicionVenta cv
                INNER JOIN cp.tipoComprobante tc
                INNER JOIN cp.venta as v
                INNER JOIN v.tipoVenta as tv
                INNER JOIN v.cliente as c
                WHERE c.negocio = :negocio AND tv.codigo= :codigo AND cp.fEmision between :fDesde AND :fHasta";
        $query = $em->createQuery($dql)
        ->setParameter(':codigo','COMPROBANTE')
        ->setParameter(':negocio',$negocio)
        ->setParameter(':fDesde',$fechaDesde)
        ->setParameter(':fHasta',$fechaHasta);
        return $query->getArrayResult();
    }

    public function comprobantesReporte($negocio,$fechaDesde,$fechaHasta){
        $em = $this->getEntityManager();
        $dql = "SELECT COUNT(cp) as recibos
                FROM App:Comprobante as cp
                INNER JOIN cp.condicionVenta cv
                INNER JOIN cp.tipoComprobante tc
                INNER JOIN cp.venta as v
                INNER JOIN v.cliente as c
                WHERE c.negocio = :negocio AND tc.codigo = :codigo AND cp.fEmision between :fDesde AND :fHasta";
        $query = $em->createQuery($dql)
        ->setParameter(':codigo','R')
        ->setParameter(':negocio',$negocio)
        ->setParameter(':fDesde',$fechaDesde)
        ->setParameter(':fHasta',$fechaHasta);
        $recibos = ($query->getSingleResult())['recibos'];

        $dql = "SELECT COUNT(cp) as facturas
        FROM App:Comprobante as cp
        INNER JOIN cp.condicionVenta cv
        INNER JOIN cp.tipoComprobante tc
        INNER JOIN cp.venta as v
        INNER JOIN v.cliente as c
        WHERE c.negocio = :negocio AND tc.codigo <> :codigo AND cp.fEmision between :fDesde AND :fHasta";
        $query = $em->createQuery($dql)
        ->setParameter(':codigo','R')
        ->setParameter(':negocio',$negocio)
        ->setParameter(':fDesde',$fechaDesde)
        ->setParameter(':fHasta',$fechaHasta);
        $facturas = ($query->getSingleResult())['facturas'];
        return ['recibos'=>$recibos,'facturas'=>$facturas];
    }

}