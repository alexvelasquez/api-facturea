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

    /** Retorno los tipos de comprobantes dada la condicion frente al IVA del cliente */
    public function compras($cliente)
    {
      $em = $this->getEntityManager();
      $dql = "SELECT ev estado_venta, v , e, SUM(pv.subtotal) as total
                FROM App:EstadoVenta as ev
                INNER JOIN ev.venta as v
                INNER JOIN App:ProductoVenta as pv WITH (pv.venta = v )
                INNER JOIN ev.estado as e
                WHERE ev.vigente = :vigente AND
                      v.cliente = :cliente AND
                      v.fHasta IS NULL AND
                      (e.codigo = :pendientePago OR e.codigo = :pendienteComprobante)
                GROUP BY ev, v, e";
      $query = $em->createQuery($dql)
      ->setParameter(':cliente',$cliente)
      ->setParameter(':pendientePago','PENDIENTEPAGO')
      ->setParameter(':pendienteComprobante','PENDIENTECOMPROBANTE')
      ->setParameter(':vigente','S');
      return $query->getArrayResult();
    }

    public function ventasPendientePago($cliente){
      $em = $this->getEntityManager();
      $dql = "SELECT ev as estado_venta,v, SUM(pv.subtotal) as total
                FROM App:EstadoVenta as ev
                INNER JOIN ev.venta as v
                INNER JOIN App:ProductoVenta pv WITH pv.venta = v
                INNER JOIN ev.estado as e
                WHERE ev.vigente = :vigente AND
                      v.fHasta IS NULL AND
                      v.cliente = :cliente AND
                      e.codigo = :codigoEstado
                GROUP BY ev,v
                ORDER BY total ASC";
      $query = $em->createQuery($dql)
      ->setParameter(':codigoEstado','PENDIENTEPAGO')
      ->setParameter(':vigente','S')
      ->setParameter(':cliente',$cliente);
      return $query->getArrayResult();
    }

    public function recaudacionReporte($negocio,$fechaDesde, $fechaHasta){
      $em = $this->getEntityManager();
      /** A COBRAR */
      $dql = "SELECT SUM(pv.subtotal) as a_cobrar 
              FROM App:EstadoVenta as ev
              INNER JOIN ev.venta as v
              INNER JOIN v.cliente as c
              INNER JOIN App:CuentaCorriente cc WITH cc.cliente = c
              INNER JOIN App:ProductoVenta pv WITH pv.venta = v
              INNER JOIN ev.estado as e
              WHERE ev.vigente = :vigente AND
                    c.negocio = :negocio AND
                    v.fHasta IS NULL AND
                    v.fVenta BETWEEN :fechaDesde AND :fechaHasta AND
                    e.codigo = :pendientePago";
      $query = $em->createQuery($dql)
      ->setParameter(':negocio',$negocio)
      ->setParameter(':fechaDesde',$fechaDesde)
      ->setParameter(':fechaHasta',$fechaHasta)
      ->setParameter(':pendientePago','PENDIENTEPAGO')
      ->setParameter(':vigente','S');
      $aCobrar = ($query->getSingleResult())['a_cobrar'];

      /** COBRAR */
      $dql = "SELECT SUM(pv.subtotal) as cobrado 
      FROM App:EstadoVenta as ev
      INNER JOIN ev.venta as v
      INNER JOIN v.cliente as c
      INNER JOIN App:CuentaCorriente cc WITH cc.cliente = c
      INNER JOIN App:ProductoVenta pv WITH pv.venta = v
      INNER JOIN ev.estado as e
      WHERE ev.vigente = :vigente AND
            c.negocio = :negocio AND
            v.fHasta IS NULL AND
            v.fVenta BETWEEN :fechaDesde AND :fechaHasta AND
            (e.codigo = :pendienteComprobante OR e.codigo = :pagado)";
      $query = $em->createQuery($dql)
      ->setParameter(':negocio',$negocio)
      ->setParameter(':fechaDesde',$fechaDesde)
      ->setParameter(':fechaHasta',$fechaHasta)
      ->setParameter(':pendienteComprobante','PENDIENTECOMPROBANTE')
      ->setParameter(':pagado','PAGADO')
      ->setParameter(':vigente','S');

      $cobrado = ($query->getSingleResult())['cobrado'];

      return ['aCobrar'=>$aCobrar, 'cobrado'=>$cobrado];
    }


    public function pedidosReporte($negocio,$fechaDesde, $fechaHasta)
    {
      $em = $this->getEntityManager();
      $dql = "SELECT count(ev) as pendientes
                FROM App:EstadoVenta as ev
                INNER JOIN ev.venta as v
                INNER JOIN ev.estado as e
                INNER JOIN v.cliente as c
                INNER JOIN v.tipoVenta as tv
                WHERE ev.vigente = :vigente AND
                      v.fHasta IS NULL AND
                      v.fVenta BETWEEN :fechaDesde AND :fechaHasta AND
                      c.negocio = :negocio AND
                      tv.codigo = :codigoVenta AND
                      e.codigo = :codigoEstado";
      $query = $em->createQuery($dql)
      ->setParameter(':negocio',$negocio)
      ->setParameter(':fechaDesde',$fechaDesde)
      ->setParameter(':fechaHasta',$fechaHasta)
      ->setParameter(':codigoEstado','PENDIENTE')
      ->setParameter(':codigoVenta','PEDIDO')
      ->setParameter(':vigente','S');
      $pendientes = ($query->getSingleResult())['pendientes'];

      $dql = "SELECT count(ev) as realizados
                FROM App:EstadoVenta as ev
                INNER JOIN ev.venta as v
                INNER JOIN ev.estado as e
                INNER JOIN v.cliente as c
                INNER JOIN v.tipoVenta as tv
                WHERE ev.vigente = :vigente AND
                      v.fHasta IS NULL AND
                      v.fVenta BETWEEN :fechaDesde AND :fechaHasta AND
                      c.negocio = :negocio AND
                      tv.codigo = :codigoVenta AND
                      e.codigo = :codigoEstado";
      $query = $em->createQuery($dql)
      ->setParameter(':negocio',$negocio)
      ->setParameter(':fechaDesde',$fechaDesde)
      ->setParameter(':fechaHasta',$fechaHasta)
      ->setParameter(':codigoEstado','REALIZADO')
      ->setParameter(':codigoVenta','PEDIDO')
      ->setParameter(':vigente','S');
      $realizados = ($query->getSingleResult())['realizados'];

      return ['pendientes'=>$pendientes,'realizados'=>$realizados];
    }

    public function comprasGraficos($negocio,$fechaDesde, $fechaHasta){
      $em = $this->getEntityManager();
      /** A COBRAR */
      $dql = "SELECT ev.estadoVentaId as estado_venta, DATE_FORMAT(v.fVenta,'%d/%m/%Y') as fecha, SUM(pv.subtotal) as total
              FROM App:EstadoVenta as ev
              INNER JOIN ev.venta as v
              INNER JOIN v.cliente as c
              INNER JOIN App:CuentaCorriente cc WITH cc.cliente = c
              INNER JOIN App:ProductoVenta pv WITH pv.venta = v
              INNER JOIN ev.estado as e
              WHERE ev.vigente = :vigente AND
                    c.negocio = :negocio AND
                    v.fHasta IS NULL AND
                    v.fVenta BETWEEN :fechaDesde AND :fechaHasta AND
                    (e.codigo = :pagado OR e.codigo = :pendienteComprobante)
              GROUP BY estado_venta,fecha";
      $query = $em->createQuery($dql)
      ->setParameter(':negocio',$negocio)
      ->setParameter(':fechaDesde',$fechaDesde)
      ->setParameter(':fechaHasta',$fechaHasta)
      ->setParameter(':pendienteComprobante','PENDIENTECOMPROBANTE')
      ->setParameter(':pagado','PAGADO')
      ->setParameter(':vigente','S');
      return $query->getArrayResult();
      // $aCobrar = ($query->getSingleResult())['a_cobrar'];
    }
}
