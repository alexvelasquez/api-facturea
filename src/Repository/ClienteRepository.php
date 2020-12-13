<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class ClienteRepository extends EntityRepository
{

    public function montoDebido($cliente){
      $em = $this->getEntityManager();
      // throw new Exception($estadoPreventa->getDescripcion());
      $qb = $em->createQueryBuilder();
      $qb->select('SUM(p.montoDebido) as monto')
          ->from('App:Cliente','c')
          ->leftJoin('App:Preventa','p','WITH','p.cliente = c')
          ->where('c = :cliente')
          ->setParameter(':cliente',$cliente);
      return  $qb->getQuery()->getOneOrNullResult();
    }

    public function cuentaCorriente($cliente,$estadoPendientePago)
    {

      $em = $this->getEntityManager();
      // throw new Exception($estadoPreventa->getDescripcion());
      $qb = $em->createQueryBuilder();
      $qbtotal = $em->createQueryBuilder();
      $qb->select('NEW ResponseCuentaCorriente(p.preventaId,
                                               p.fecha,
                                               p.montoDebido,
                                               SUM(pp.subtotal),
                                               p.fModificacion)')
          ->from('App:Preventa','p')
          ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
          ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
          ->innerJoin('App:ProductoPreventa', 'pp','WITH', 'p = pp.preventa')
          ->innerJoin('App:Estado', 'e','WITH', 'e = cp.estado')
          ->where('c.negocio = :negocio')
          ->andWhere('cp.estado = :estado')
          ->andWhere('p.cliente = :cliente')
          ->groupBy('p.preventaId, p.fecha, p.montoDebido')
          ->orderBy('p.fecha','DESC')
          ->setParameter(':cliente',$cliente)
          ->setParameter(':estado',$estadoPendientePago)
          ->setParameter(':negocio',$cliente->getNegocio());
          $response['cuentas'] = $qb->getQuery()->getArrayResult();

          $qbtotal->select('c.clienteId, c.razonSocial as cliente, MAX(m.fCreacion) fUltimoMovimiento,SUM(p.montoDebido) as deuda')
              ->from('App:Preventa','p')
              ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
              ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
              ->innerJoin('App:ProductoPreventa', 'pp','WITH', 'p = pp.preventa')
              ->leftJoin('App:Movimiento', 'm','WITH', 'c = m.cliente')
              ->innerJoin('App:Estado', 'e','WITH', 'e = cp.estado')
              ->where('c.negocio = :negocio')
              ->andWhere('cp.estado = :estado')
              ->andWhere('p.cliente = :cliente')
              ->andWhere('cp.vigente = :vigente')
              ->groupBy('c.clienteId')
              ->setParameter(':cliente',$cliente)
              ->setParameter(':estado',$estadoPendientePago)
              ->setParameter(':vigente','S')
              ->setParameter(':negocio',$cliente->getNegocio());
        $response['total'] = $qbtotal->getQuery()->getOneOrNullResult();
      return $response;
    }

    public function comprasPendientes($cliente,$estadoPendientePago)
    {
      $em = $this->getEntityManager();
      // throw new Exception($estadoPreventa->getDescripcion());
      $qb = $em->createQueryBuilder();
      $qbtotal = $em->createQueryBuilder();
      $qb->select('NEW ResponseCuentaCorriente(p.preventaId,
                                               p.fecha,
                                               p.montoDebido,
                                               SUM(pp.subtotal),
                                               p.fModificacion)')
          ->from('App:Preventa','p')
          ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
          ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
          ->innerJoin('App:ProductoPreventa', 'pp','WITH', 'p = pp.preventa')
          ->innerJoin('App:Estado', 'e','WITH', 'e = cp.estado')
          ->where('c.negocio = :negocio')
          ->andWhere('cp.estado = :estado')
          ->andWhere('p.cliente = :cliente')
          ->andWhere('cp.vigente = :vigente')
          ->groupBy('p.preventaId')
          ->setParameter(':cliente',$cliente)
          ->setParameter(':estado',$estadoPendientePago)
          ->setParameter(':vigente','S')
          ->setParameter(':negocio',$cliente->getNegocio());
          $response['cuentas'] = $qb->getQuery()->getArrayResult();
      return $response;
    }



}
