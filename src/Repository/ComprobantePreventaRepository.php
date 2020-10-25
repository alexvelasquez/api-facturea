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

    public function  recaudacionTotal($negocio,$pagado,$pendientePago,$periodo){
      $em = $this->getEntityManager();
      $qb = $em->createQueryBuilder();
      $qb->select("e.estadoId as estado,DATE_FORMAT(p.fecha,'%d/%m/%Y') as fecha, SUM(pp.subtotal) as total")
          ->from('App:Preventa','p')
          ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
          ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
          ->innerJoin('App:ProductoPreventa', 'pp','WITH', 'p = pp.preventa')
          ->innerJoin('App:Estado', 'e','WITH', 'cp.estado = e')
          ->where('cp.vigente = :vigente')
          ->andWhere('c.negocio = :negocio')
          ->andWhere('cp.estado = :pagado OR cp.estado = :pendientePago')
          ->andWhere('p.fecha BETWEEN :fechaDesde AND :fechaHasta')
          ->groupBy('e.estadoId,p.fecha')
          ->setParameter(':vigente','S')
          ->setParameter(':pagado',$pagado)
          ->setParameter(':pendientePago',$pendientePago)
          ->setParameter(':fechaDesde',$periodo['fechaDesde'])
          ->setParameter(':fechaHasta',$periodo['fechaHasta'])
          ->setParameter(':negocio',$negocio);
      return $qb->getQuery()->getResult();
    }

    public function  comprobantesTotales($negocio,$pagado,$periodo){
      $em = $this->getEntityManager();
      $qb = $em->createQueryBuilder();
      $qb->select("tc.tipoComprobanteId as tipoComprobante, COUNT(tc.tipoComprobanteId) as cantidad")
          ->from('App:Preventa','p')
          ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
          ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
          ->innerJoin('App:TipoComprobante', 'tc','WITH', 'cp.tipoComprobante = tc')
          ->where('cp.vigente = :vigente')
          ->andWhere('c.negocio = :negocio')
          ->andWhere('cp.estado = :pagado')
          ->andWhere('p.fecha BETWEEN :fechaDesde AND :fechaHasta')
          ->groupBy('tc.tipoComprobanteId')
          ->setParameter(':vigente','S')
          ->setParameter(':pagado',$pagado)
          ->setParameter(':fechaDesde',$periodo['fechaDesde'])
          ->setParameter(':fechaHasta',$periodo['fechaHasta'])
          ->setParameter(':negocio',$negocio);
      return $qb->getQuery()->getResult();
    }


    public function  pedidosTotales($negocio,$pagado,$realizado,$pendiente,$tipoPreventa,$periodo){
      $em = $this->getEntityManager();
      $qb = $em->createQueryBuilder();
      $qb->select("e.estadoId as estado, COUNT(p.preventaId) as cantidad")
          ->from('App:Preventa','p')
          ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
          ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
          ->innerJoin('App:Estado', 'e','WITH', 'cp.estado = e')
          ->where('cp.vigente = :vigente')
          ->andWhere('c.negocio = :negocio')
          ->andWhere('p.tipoPreventa = :tipoPreventa')
          ->andWhere('cp.estado = :pagado OR cp.estado = :realizado OR cp.estado = :pendiente')
          ->andWhere('p.fecha BETWEEN :fechaDesde AND :fechaHasta')
          ->groupBy('e.estadoId')
          ->setParameter(':vigente','S')
          ->setParameter(':pagado',$pagado)
          ->setParameter(':realizado',$realizado)
          ->setParameter(':pendiente',$pendiente)
          ->setParameter(':tipoPreventa',$tipoPreventa)
          ->setParameter(':fechaDesde',$periodo['fechaDesde'])
          ->setParameter(':fechaHasta',$periodo['fechaHasta'])
          ->setParameter(':negocio',$negocio);
      return $qb->getQuery()->getResult();
    }


}
