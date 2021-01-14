<?php

namespace App\Repository;


use Doctrine\ORM\EntityRepository;

class VentaRepository extends EntityRepository
{
    /** Retorno los tipos de comprobantes dada la condicion frente al IVA del cliente */
    public function pedidos($negocio,$codigoEstado)
    {
      $em = $this->getEntityManager();
      $dql = "SELECT v.ventaId as venta, v.fVenta as fecha, c.razonSocial as cliente , e.codigo as estado
                FROM App:EstadoVenta as ev
                INNER JOIN ev.venta as v
                INNER JOIN ev.estado as e
                INNER JOIN v.cliente as c
                INNER JOIN v.tipoVenta as tv
                WHERE ev.vigente = :vigente AND
                      v.fHasta IS NULL AND
                      c.negocio = :negocio AND
                      tv.codigo = :codigoVenta AND
                      e.codigo = :codigoEstado";
      $query = $em->createQuery($dql)
      ->setParameter(':negocio',$negocio)
      ->setParameter(':codigoEstado',$codigoEstado)
      ->setParameter(':codigoVenta','PEDIDO')
      ->setParameter(':vigente','S');
      return $query->getArrayResult();
    }
}
