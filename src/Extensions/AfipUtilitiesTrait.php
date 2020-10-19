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
            $id = $producto['alicuota']['afip_id'];
            $alicuotas['ali'.$id]['Id']=$id;
            /** BASE IMPONIBLE **/
            $alicuotas['ali'.$id]['BaseImp'] = array_key_exists('Importe',$alicuotas['ali'.$id]) ? $alicuotas['ali'.$id]['BaseImp'] : 0;
            $alicuotas['ali'.$id]['BaseImp'] +=  $producto['subtotal_sin_iva'];
            $alicuotas['ali'.$id]['Importe'] = array_key_exists('Importe',$alicuotas['ali'.$id]) ? $alicuotas['ali'.$id]['Importe'] : 0;
            $alicuotas['ali'.$id]['Importe'] += $producto['monto_iva'];
      }
      return $alicuotas;
  }
}
