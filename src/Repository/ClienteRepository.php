<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class ClienteRepository extends EntityRepository
{
    /** Retorno los tipos de comprobantes dada la condicion frente al IVA del cliente */
    public function cuentaCorriente($cliente,$negocio,$estadoPendientePago)
    {

      $em = $this->getEntityManager();
      // throw new Exception($estadoPreventa->getDescripcion());
      $qb = $em->createQueryBuilder();
      $qbtotal = $em->createQueryBuilder();
      $qb->select('NEW App:ResponseCuentaCorriente(cc.cuentaCorrienteId,
                                                  p.preventaId,
                                                  p.fecha,
                                                  SUM(pp.subtotal),
                                                  cc.montoPagado,
                                                  p.fModificacion)')
          ->from('App:Preventa','p')
          ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
          ->innerJoin('App:CuentaCorriente','cc','WITH','p = cc.preventa')
          ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
          ->innerJoin('App:ProductoPreventa', 'pp','WITH', 'p = pp.preventa')
          ->innerJoin('App:Estado', 'e','WITH', 'e = cp.estado')
          ->where('cp.vigente = :vigente')
          ->andWhere('c.negocio = :negocio')
          ->andWhere('cp.estado = :estado')
          ->andWhere('p.cliente = :cliente')
          ->groupBy('cc.cuentaCorrienteId')
          ->orderBy('p.fecha','DESC')
          ->setParameter(':cliente',$cliente)
          ->setParameter(':estado',$estadoPendientePago)
          ->setParameter(':negocio',$negocio)
          ->setParameter(':vigente','S');
          $response['cuentas'] = $qb->getQuery()->getArrayResult();

          $qbtotal->select('c.razonSocial as cliente, MAX(cc.fModificacion) fUltimoMovimiento, (SUM(pp.subtotal) - cc.montoPagado) as deuda ')
              ->from('App:Preventa','p')
              ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
              ->innerJoin('App:CuentaCorriente','cc','WITH','p = cc.preventa')
              ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
              ->innerJoin('App:ProductoPreventa', 'pp','WITH', 'p = pp.preventa')
              ->innerJoin('App:Estado', 'e','WITH', 'e = cp.estado')
              ->where('cp.vigente = :vigente')
              ->andWhere('c.negocio = :negocio')
              ->andWhere('cp.estado = :estado')
              ->andWhere('p.cliente = :cliente')
              ->groupBy('c.clienteId')
              ->orderBy('p.fecha','DESC')
              ->setParameter(':cliente',$cliente)
              ->setParameter(':estado',$estadoPendientePago)
              ->setParameter(':negocio',$negocio)
              ->setParameter(':vigente','S');
        $response['total'] = $qbtotal->getQuery()->getSingleResult();
      return $response;
    }


}
