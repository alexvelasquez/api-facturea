<?php

namespace App\Repository;
use Doctrine\ORM\EntityRepository;
use App\Entity\ProductoVenta;

class ProductoVentaRepository extends EntityRepository
{
    /** Genero los productos preventas */
    public function generarProductosVentas($productos,$venta)
    {
      $em = $this->getEntityManager();
      foreach ($productos as $value) {
        $producto = $em->getRepository("App:Producto")->find($value['producto']);
        $cantidad = $value['cantidad'];
        if($producto->getStock() < $cantidad){
          throw new \Exception('La cantidad del producto supera el stock actual');
        }
        /** actualizo el stock actual **/
        $producto->setStock($producto->getStock() - $cantidad);
        $subtotal = $value['subtotal'];
        $subtotalSinIva = $value['subtotal_sin_iva'];
        $montoBonif = $value['monto_bonif'];
        $montoIva = $value['monto_iva'] ?? null;
        $precioUnitario = $value['precio_unitario'];
        $bonificacion = $value['bonificacion'];
        $alicuota = !empty($value['tipo_alicuota']) ? $em->getRepository("App:TipoAliCuota")->find($value['tipo_alicuota']) : null;
        $productoVenta = new ProductoVenta($cantidad,$subtotal,$subtotalSinIva,$bonificacion,$montoBonif,$precioUnitario,$producto,$venta,$alicuota,$montoIva);
        $em->persist($productoVenta);
      }
    }


    /** Proceso los productos preventas */
    public function editarProductosVentas($productos,$venta)
    {
      $em = $this->getEntityManager();
      foreach ($productos as $value) {
        /** si el producto tiene id lo modifico*/
        if(!empty($value['producto_venta_id'])){
          $productoVenta = $em->getRepository("App:ProductoVenta")->find($value['producto_venta_id']);
          $producto = $em->getRepository("App:Producto")->find($value['producto']);
          $cantidad = $value['cantidad'];
          if($producto->getStock() < $cantidad ){
            throw new \Exception('La cantidad del producto supera el stock actual');
          }
          /** modifico el stock actual **/
          $producto = $this->calcularStock($producto,$productoVenta,$cantidad);
          $productoVenta->setCantidad($value['cantidad']);
          $productoVenta->setSubtotal($value['subtotal']);
          $productoVenta->setSubtotalSinIva($value['subtotal_sin_iva']);
          $productoVenta->setMontoBonif($value['monto_bonif']);
          $productoVenta->setMontoIva($value['monto_iva'] ?? null);
          $productoVenta->setPrecioUnitario($value['precio_unitario']);
          $productoVenta->setBonificacion($value['bonificacion']);
          $alicuota = !empty($value['tipo_alicuota']) ? $em->getRepository("App:TipoAliCuota")->find($value['tipo_alicuota']) : null;
          $productoVenta->setTipoAlicuota($alicuota);
        }
        else{
          $producto = $em->getRepository("App:Producto")->find($value['producto']);
          $cantidad = $value['cantidad'];
          if($producto->getStock() < $cantidad ){
            throw new \Exception('La cantidad del producto supera el stock actual');
          }
          $subtotal = $value['subtotal'];
          $subtotalSinIva = $value['subtotal_sin_iva'];
          $montoBonif = $value['monto_bonif'];
          $montoIva = $value['monto_iva'] ?? null;
          $precioUnitario = $value['precio_unitario'];
          $bonificacion = $value['bonificacion'];
          $alicuota = !empty($value['tipo_alicuota']) ? $em->getRepository("App:TipoAliCuota")->find($value['alicuota']) : null;
          $productoVenta = new ProductoVenta($cantidad,$subtotal,$subtotalSinIva,$bonificacion,$montoBonif,$precioUnitario,$producto,$venta,$alicuota,$montoIva);
          $em->persist($productoVenta);
        }
      }
    }

    /** eliminos los productos ventas de la base */
    public function eliminarProductosVentas($productos){
      $em = $this->getEntityManager();
      foreach ($productos as $value) {
        $productoVenta = $em->getRepository("App:ProductoVenta")->find($value['producto_venta_id']);
        $producto = $em->getRepository("App:Producto")->find($value['producto']);
        $producto->setStock($producto->getStock()+$productoVenta->getCantidad());
        $em->remove($productoVenta);
      }
    }

    /** Proceso los productos preventas */
    public function restablecerProductosPreventas($productos,$preventa)
    {
      $em = $this->getEntityManager();
      foreach ($productos as $value) {
        $producto = $value->getProducto();
        $producto->setStock($producto->getStock() + $value->getCantidad());
      }
    }

    private function calcularStock($producto,$productoPreventa,$cantidad){
      if($productoPreventa->getCantidad() < $cantidad){
        $stockProducto = $producto->getStock();
        $producto->setStock($stockProducto - ($cantidad - $productoPreventa->getCantidad()));
      }
      elseif($productoPreventa->getCantidad() > $cantidad){
        $stockProducto = $producto->getStock();
        $producto->setStock($stockProducto+($cantidad - $productoPreventa->getCantidad()));
      }
      else{
        $producto->setStock($cantidad);
      }
      return $producto;
    }




}
