<?php

namespace App\Extensions;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Entity\Preventa;
use App\Entity\ComprobantePreventa;

/**
 * Este trait incluye los metodos para procesar archivos
 */
trait ComprobantesUtilitiesTrait
{
  /** Metodo que almacena los datos relacionados a la factura */
  private function registrarDatosComprobante($paramFetcher,$tipoComprobante,$afip){
    /** verifico si es recibo y obtengo el numero del recibo**/
    $ptoVta = $paramFetcher->get('cliente')['negocio']['punto_vta'];
    $tipoRecibo = ($tipoComprobante->getAfipId() == $this->getParameter('recibo'));
    if($tipoRecibo){
      $ultimoRecibo = count($this->manager()->getRepository("App:ComprobantePreventa")->findBy(['tipoComprobante'=>$tipoComprobante]));
      $nroComprobante = $ultimoRecibo;
    }
    else{
      $nroComprobante = $this->obtenerUltimoComprobante($afip, $ptoVta, $tipoComprobante->getAfipId()) + 1;
    }

    /** Preventa*/
    $fechaEmision = $paramFetcher->get('fecha_emision');
    $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente')['cliente_id']);
    $tipoPreventa = $this->manager()->getRepository("App:TipoPreventa")->find($this->getParameter('tipo_preventa_comprobante'));//Comprobante
    $preventa = new Preventa($cliente,$tipoPreventa,$fechaEmision);
    $this->manager()->persist($preventa);

    /** Productos Preventas **/
    $productos = $paramFetcher->get('productos');
    $this->manager()->getRepository("App:ProductoPreventa")->generarProductosPreventas($productos,$preventa);

    /** Comprobante Preventa*/
    $condicionVenta = $this->manager()->getRepository("App:CondicionVenta")->find($paramFetcher->get('condicion_vta'));
    $estadoComprobante = $this->getParameter('estado_pagado'); //por defecto pagado.
    if($tipoRecibo && $condicionVenta->getCondicionVentaId() == $this->getParameter('cuenta_corriente')){ //verico si es un recibo y se aplica a cuenta corriente..
      $estadoComprobante = $this->getParameter('estado_pendiente_pago'); // cambio el estado a pendiente de pago
      $preventa->setMontoDebido($paramFetcher->get('importes')['total']); // seteo el monto debido de la compra
    }
    $estadoComprobante =  $this->manager()->getRepository("App:Estado")->find($estadoComprobante);
    $comprobantePreventa = new ComprobantePreventa($preventa,$estadoComprobante,$tipoComprobante,$condicionVenta,$nroComprobante,$ptoVta);
    $this->manager()->persist($comprobantePreventa);
    return $comprobantePreventa;
  }

/** Parametros para la generacion del PDF**/
  private function parametrosComprobantePDF($paramFetcher,$data,$tipoComprobante,$comprobantePreventa,$cae=null){
    // dd($cae);
    $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente')['cliente_id']);
    $condicionVenta = $this->manager()->getRepository("App:CondicionVenta")->find($paramFetcher->get('condicion_vta'));
    $data['cliente'] = $cliente;
    $data['productos'] =$paramFetcher->get('productos');
    $data['CbteTipo'] = $tipoComprobante;
    $data['compPrev'] = $comprobantePreventa;
    $data['CbteFch'] 	= intval(date('Ymd',strtotime($paramFetcher->get('fecha_emision'))));
    $data['ImpNeto'] = $paramFetcher->get('importes')['gravado'];
    $data['ImpTotal'] = $paramFetcher->get('importes')['total'];
    $data['CondVta'] = $condicionVenta;
    $data['Iva'] = [];
    if($tipoComprobante->getAfipId() == 1){
      $alicuotas = $this->obtenerAliCuotasTotales($productos,$paramFetcher->get('importes')['gravado']);
      $data['Iva'] = array_values($alicuotas);
    }
    if($tipoComprobante->getAfipId() != $this->getParameter('recibo')){
      /**agrego el codigo de barras */
      $data['cae'] = $cae;
      $digitoVerificador = substr($data['cliente']->getNegocio()->getCuitCuil(), -1);
      $codigo = $cliente->getNegocio()->getCuitCuil().$data['CbteTipo']->getAfipId().$data['PtoVta'].$cae['CAE'].$digitoVerificador;
      $generator = new BarcodeGeneratorPNG();
      $codigoBarras = 'data:image/png;base64,'.base64_encode($generator->getBarcode($codigo, $generator::TYPE_CODE_128));
      $data['codigo'] = $codigo;
      $data['codigoBarras'] = $codigoBarras;
    }
    return $data;
  }
}
