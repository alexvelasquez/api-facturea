<?php

namespace App\Extensions;

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
  protected function obtenerMontosTotalesFactura($productos,$tipoComprobante)
  {
      $response = array('netoGravado' => 0,
                        'netoNoGravado' => 0,
                        'exento' => 0,
                        'IVA'=>['alicuotas'=>[],'total'=>0]);

      foreach ($productos as $producto) {
          if(!empty($producto['iva'])){
            switch ($producto['iva']['afip_id']) {
              //No Gravado
              case 1:
                $response['netoNoGravado'] += $producto['subtotal'];
                break;
                //exento
              case 2:
                $response['exento'] += $producto['subtotal'];
                break;
                //0%
              case 3:
                $alicuota0['Id'] = $producto['iva']['afip_id'];
                $alicuota0['Importe'] += $producto['monto_iva'];
                $response['IVA']['total'] += $producto['monto_iva'];
                break;
              //0.25%
              case 9:
                $alicuota025['Id'] = $producto['iva']['afip_id'];
                $alicuota025['Importe'] += $producto['monto_iva'];
                $response['IVA']['total'] += $producto['monto_iva'];
                break;
              //0.05%
              case 8:
                $alicuota05['Id'] = $producto['iva']['afip_id'];
                $alicuota05['Importe'] += $producto['monto_iva'];
                $response['IVA']['total'] += $producto['monto_iva'];
                break;
              //10.5%
              case 4:
                $alicuota105['Id'] = $producto['iva']['afip_id'];
                $alicuota105['Importe'] += $producto['monto_iva'];
                $response['IVA']['total'] += $producto['monto_iva'];
                break;
              //21%
              case 5:
                $alicuota21['Id'] = $producto['iva']['afip_id'];
                $alicuota21['Importe'] += $producto['monto_iva'];
                $response['IVA']['total'] += $producto['monto_iva'];
                break;
              //27%
              case 5:
                $alicuota27['Id'] = $producto['iva']['afip_id'];
                $alicuota27['Importe'] += $producto['monto_iva'];
                $response['IVA']['total'] += $producto['monto_iva'];
                break;
            }
          }
          else{
            $response['netoGravado'] += $producto['subtotal'];
          }
          /** asigno base imponible*/
          empty($alicuota0) ?? $alicuota0['BaseImp'] = $response['netoGravado'];
          empty($alicuota025) ?? $alicuota025['BaseImp'] = $response['netoGravado'];
          empty($alicuota05) ?? $alicuota05['BaseImp'] = $response['netoGravado'];
          empty($alicuota105) ?? $alicuota105['BaseImp'] = $response['netoGravado'];
          empty($alicuota21) ?? $alicuota21['BaseImp'] = $response['netoGravado'];
          empty($alicuota27) ?? $alicuota27['BaseImp'] = $response['netoGravado'];

          /** se hace esto,para poder pasarle los alicuotas en ese formato a al metodop que crea el comporbante */
          empty($alicuota0) ?? $response['IVA']['alicuotas'][]=$alicuota0;
          empty($alicuota025) ?? $response['IVA']['alicuotas'][]=$alicuota025;
          empty($alicuota05) ?? $response['IVA']['alicuotas'][]=$alicuota05;
          empty($alicuota105) ?? $response['IVA']['alicuotas'][]=$alicuota105;
          empty($alicuota21) ?? $response['IVA']['alicuotas'][]=$alicuota21;
          empty($alicuota27) ?? $response['IVA']['alicuotas'][]=$alicuota27;

      }

      return $response;
  }
}
