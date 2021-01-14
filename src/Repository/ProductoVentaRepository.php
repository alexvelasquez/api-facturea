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
        $producto = $em->getRepository("App:Producto")->find($value['producto']['producto_id']);
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
        $alicuota = !empty($value['alicuota']) ? $em->getRepository("App:TipoAliCuota")->find($value['alicuota']['tipo_alicuota_id']) : null;
        $productoVenta = new ProductoVenta($cantidad,$subtotal,$subtotalSinIva,$bonificacion,$montoBonif,$precioUnitario,$producto,$venta,$alicuota,$montoIva);
        $em->persist($productoVenta);
      }
    }


    /** Proceso los productos preventas */
    public function editarProductosPreventas($productos,$preventa)
    {
      $em = $this->getEntityManager();
      foreach ($productos as $value) {
        /** si el producto tiene id lo modifico*/
        if(!empty($value['producto_preventa_id'])){
          $productoPreventa = $em->getRepository("App:ProductoPreventa")->find($value['producto_preventa_id']);
          $producto = $em->getRepository("App:Producto")->find($value['producto']['producto_id']);
          $cantidad = $value['cantidad'];
          if($producto->getStock() < $cantidad ){
            throw new \Exception('La cantidad del producto supera el stock actual');
          }
          /** modifico el stock actual **/
          $producto = $this->calcularStock($producto,$productoPreventa,$cantidad);
          $productoPreventa->setCantidad($value['cantidad']);
          $productoPreventa->setSubtotal($value['subtotal']);
          $productoPreventa->setSubtotalSinIva($value['subtotal_sin_iva']);
          $productoPreventa->setMontoBonif($value['monto_bonif']);
          $productoPreventa->setMontoIva($value['monto_iva'] ?? null);
          $productoPreventa->setPrecioUnitario($value['precio_unitario']);
          $productoPreventa->setBonificacion($value['bonificacion']);
          $alicuota = !empty($value['alicuota']) ? $em->getRepository("App:TipoAliCuota")->find($value['alicuota']['tipo_alicuota_id']) : null;
          $productoPreventa->setAliCuota($alicuota);
        }
        else{
          $producto = $em->getRepository("App:Producto")->find($value['producto']['producto_id']);
          $cantidad = $value['cantidad'];
          if($producto->getStock() < $cantidad ){
            throw new \Exception('La cantidad del producto supera el stock actual');
          }
          $subtotal = $value['subtotal'];
          $montoBonif = $value['monto_bonif'];
          $montoIva = $value['monto_iva'] ?? null;
          $precioUnitario = $value['precio_unitario'];
          $bonificacion = $value['bonificacion'];
          $subtotalSinIva = $value['subtotal_sin_iva'];
          $alicuota = !empty($value['alicuota']) ? $em->getRepository("App:TipoAliCuota")->find($value['alicuota']['tipo_alicuota_id']) : null;
          $productoPreventa = new ProductoVenta($cantidad,$subtotal,$subtotalSinIva,$bonificacion,$montoBonif,$precioUnitario,$producto,$preventa,$alicuota,$montoIva);
          $em->persist($productoPreventa);
        }
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
