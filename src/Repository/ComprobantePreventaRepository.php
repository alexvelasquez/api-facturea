<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;


class ComprobantePreventaRepository extends EntityRepository
{
    /** Retorno los tipos de comprobantes dada la condicion frente al IVA del cliente */
    public function comprobantes($negocio,$fechaDesde,$fechaHasta)
    {
      $em = $this->getEntityManager();
      // throw new Exception($estadoPreventa->getDescripcion());
      $qb = $em->createQueryBuilder();
      $qb->select('NEW App:ResponsePreventa(p.preventaId,
                                            c.razonSocial,
                                            p.fecha,
                                            e.descripcion,
                                            tc.descripcion,
                                            cp.numero,
                                            cp.puntoVenta,
                                            cv.descripcion)')
          ->from('App:Preventa','p')
          ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
          ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
          ->innerJoin('App:TipoComprobante', 'tc','WITH', 'tc = cp.tipoComprobante')
          ->innerJoin('App:CondicionVenta', 'cv','WITH', 'cv = cp.condicionVenta')
          ->innerJoin('App:Estado', 'e','WITH', 'e = cp.estado')
          ->where('cp.vigente = :vigente')
          ->andWhere('c.negocio = :negocio')
          ->andWhere('tc.tipoComprobanteId IS NOT NULL')
          ->andWhere('p.fecha BETWEEN :fDesde AND :fHasta')
          ->setParameter(':vigente','S')
          ->setParameter(':fDesde',$fechaDesde)
          ->setParameter(':fHasta',$fechaHasta)
          ->setParameter(':negocio',$negocio)
          ->orderBy('p.fecha','DESC');
      return $qb->getQuery()->getResult();
    }

    public function  ventasTotales($negocio,$estados){
      $em = $this->getEntityManager();
      $pagado = $estados['pagado'];
      $pendientePago =  $estados['pendientePago'];
      $qb = $em->createQueryBuilder();
      $qb->select('e.estadoId as estado','SUM(pp.subtotal) as total', 'MONTH(p.fecha) as mes')
          ->from('App:Preventa','p')
          ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
          ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
          ->innerJoin('App:ProductoPreventa', 'pp','WITH', 'p = pp.preventa')
          ->innerJoin('App:Estado', 'e','WITH', 'cp.estado = e')
          ->where('cp.vigente = :vigente')
          ->andWhere('c.negocio = :negocio')
          ->andWhere('cp.estado = :pagado OR cp.estado = :pendientePago')
          ->groupBy('e.estadoId','mes')
          ->setParameter(':vigente','S')
          ->setParameter(':pagado',$pagado)
          ->setParameter(':pendientePago',$pendientePago)
          ->setParameter(':negocio',$negocio);
      return $qb->getQuery()->getResult();
    }


}
