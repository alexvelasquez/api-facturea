<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class PreventaRepository extends EntityRepository
{
    /** Retorno los tipos de comprobantes dada la condicion frente al IVA del cliente */
    public function preventasTipo($negocio,$tipoPreventa,$estado=null)
    {

      $em = $this->getEntityManager();
      // throw new Exception($estadoPreventa->getDescripcion());
      $qb = $em->createQueryBuilder();
      $qb->select('NEW App:ResponsePreventa(p.preventaId,c.razonSocial,p.fecha,e.descripcion)')
          ->from('App:Preventa','p')
          ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
          ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
          ->innerJoin('App:Estado', 'e','WITH', 'e = cp.estado')
          ->where('cp.vigente = :vigente')
          ->andWhere('p.tipoPreventa = :tipoPreventa')
          ->andWhere('c.negocio = :negocio')
          ->orderBy('p.fecha','DESC')
          ->setParameter(':negocio',$negocio)
          ->setParameter(':vigente','S')
          ->setParameter(':tipoPreventa',$tipoPreventa);
          if(!empty($estado)){
            $qb->andWhere('cp.estado = :estado')
                ->setParameter(':estado',$estado);
          }
      return $qb->getQuery()->getArrayResult();
    }

    /** Retorno los tipos de comprobantes dada la condicion frente al IVA del cliente */
    public function totalesPorEstado($negocio)
    {

      $em = $this->getEntityManager();
      // throw new Exception($estadoPreventa->getDescripcion());
      $qb = $em->createQueryBuilder();
      $qb->select('e.descripcion,COUNT(e) as total')
          ->from('App:Preventa','p')
          ->innerJoin('App:Cliente','c','WITH','c = p.cliente')
          ->innerJoin('App:ComprobantePreventa', 'cp','WITH', 'p = cp.preventa')
          ->innerJoin('App:Estado', 'e','WITH', 'e = cp.estado')
          ->where('cp.vigente = :vigente')
          ->andWhere('p.tipoPreventa = :tipoPreventa')
          ->andWhere('c.negocio = :negocio')
          ->groupBy('e.descripcion')
          ->setParameter(':vigente','S')
          ->setParameter(':negocio',$negocio)
          ->setParameter(':tipoPreventa',2);
      return $qb->getQuery()->getResult();
    }


}
