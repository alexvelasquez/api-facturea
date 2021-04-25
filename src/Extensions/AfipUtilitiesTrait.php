<?php

namespace App\Extensions;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Este trait incluye los metodos para procesar archivos
 */
trait AfipUtilitiesTrait
{

  private function obtenerUltimoComprobante($serviceAfip,$puntoVta,$tipoComprobante)
  {
    $response = $serviceAfip->getWS()->ElectronicBilling->GetLastVoucher($puntoVta,$tipoComprobante);
    return $response;
  }

  /** se procesan los productos de la factura y se calculan los montos totales*/
  protected function obtenerAliCuotasTotales($productos,$baseImp)
  {
      $alicuotas = [];
      foreach ($productos as $producto) {
            $id = $producto['tipo_alicuota']['afip_id'];
            $alicuotas['ali'.$id]['Id']=$id;
            /** BASE IMPONIBLE **/
            $alicuotas['ali'.$id]['BaseImp'] = array_key_exists('Importe',$alicuotas['ali'.$id]) ? $alicuotas['ali'.$id]['BaseImp'] : 0;
            $alicuotas['ali'.$id]['BaseImp'] +=  $producto['subtotal_sin_iva'];
            $alicuotas['ali'.$id]['Importe'] = array_key_exists('Importe',$alicuotas['ali'.$id]) ? $alicuotas['ali'.$id]['Importe'] : 0;
            $alicuotas['ali'.$id]['Importe'] += $producto['monto_iva'];
      }
      return $alicuotas;
  }

  protected function parametrosComprobanteAfip($paramFetcher,$tipoComprobante,$nroComprobante){
    $data = array(
      'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
      'PtoVta' 	=> $this->getUser()->getNegocio()->getPuntoVta(),  // Punto de venta
      'CbteTipo' 	=> intval($tipoComprobante->getAfipId()),  // Tipo de comprobante (ver tipos disponibles)
      'Concepto' 	=> $paramFetcher->get('concepto'),  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
      'DocTipo' 	=>  $paramFetcher->get('cliente')['tipo_documento']['afip_id'], // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
      'DocNro' 	=>    $paramFetcher->get('cliente')['documento'],  // Número de documento del comprador (0 consumidor final)
      'CbteDesde' 	=> $nroComprobante,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
      'CbteHasta' 	=> $nroComprobante,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
      'CbteFch' 	=>   intval(date('Ymd',strtotime($paramFetcher->get('fecha_emision')))), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
      'ImpTotal' 	=> $paramFetcher->get('importes')['total'], // Importe total del comprobante
      'ImpTotConc' 	=> $paramFetcher->get('importes')['noGravado'],   // Importe neto no gravado
      'ImpNeto' 	=> $paramFetcher->get('importes')['gravado'], // Importe neto gravado
      'ImpOpEx' 	=> $paramFetcher->get('importes')['exento'],   // Importe exento de IVA
      'ImpIVA' 	=>  $paramFetcher->get('importes')['iva'],  //Importe total de IVA
      'ImpTrib' 	=> 0,   //Importe total de tributos
      'FchServDesde' 	=> !empty($paramFetcher->get('fecha_desde')) ?  intval(date('Ymd',strtotime($paramFetcher->get('fecha_desde')))) : NULL, // (Opcional) Fecha de inicio del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
      'FchServHasta' 	=> !empty($paramFetcher->get('fecha_hasta')) ?  intval(date('Ymd',strtotime($paramFetcher->get('fecha_hasta')))) : NULL, // (Opcional) Fecha de fin del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
      'FchVtoPago' 	=> !empty($paramFetcher->get('fecha_vto')) ?  intval(date('Ymd',strtotime($paramFetcher->get('fecha_vto')))) : NULL, // (Opcional) Fecha de vencimiento del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
      'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos)
      'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)
    );
    if($tipoComprobante->getAfipId() == $this->getParameter('factura_A') || $this->getParameter('factura_B')){
        $alicuotas = $this->obtenerAliCuotasTotales($paramFetcher->get('productos'),$paramFetcher->get('importes')['gravado']);
        $data['Iva'] = array_values($alicuotas);
    }
    return $data;
  }

}
