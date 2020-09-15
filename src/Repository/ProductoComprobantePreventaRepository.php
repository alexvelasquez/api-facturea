<?php

namespace App\Repository;
use App\Entity\ProductoComprobantePreventa;

use Doctrine\ORM\EntityRepository;

class ProductoComprobantePreventaRepository extends EntityRepository
{
    /** Genro los productos preventas */
    public function generarProductosComprobantesPreventas($productos,$comprobantePreventa)
    {
      $em = $this->getEntityManager();
      dd($em);
      try {
        foreach ($productos as $value) {
          $producto = $em->getRepository("App:Producto")->find($value['producto']['producto_id']);
          $cantidad = $value['cantidad'];
          $subtotal = $value['subtotal'];
          $montoBonif = $value['monto_bonif'];
          $montoIva = $value['monto_iva'];
          $condicionIva = $em->getRepository("App:Producto")->find($value['condicion_iva']['condicion_iva_id']);
          $productoComprobantePreventa = new ProductoComprobantePreventa($cantidad,$subtotal,$comprobantePreventa,$producto,$condicionIva,$montoIva);
          dd($productoComprobantePreventa);
          $em->persist($productoComprobantePreventa);
        }
        $em->flush();
      } catch (\Exception $e) {
          throw new Exception($e);
      }

    }
}
