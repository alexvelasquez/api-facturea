<?php

namespace App\Extensions;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Picqer\Barcode\BarcodeGeneratorHTML;
use Picqer\Barcode\BarcodeGeneratorPNG;
use App\Entity\Venta;
use App\Entity\Comprobante;
use App\Entity\EstadoVenta;
use App\Entity\Movimiento;

/**
 * Este trait incluye los metodos para procesar archivos
 */
trait ComprobantesUtilitiesTrait
{
  /** Metodo que almacena los datos relacionados a la factura */
  private function registrarDatosComprobante($paramFetcher,$tipoComprobante,$afip = null){
    /** verifico si es recibo y obtengo el numero del recibo**/
    $ptoVta = $paramFetcher->get('cliente')['negocio']['punto_vta'];
    $tipoRecibo = ($tipoComprobante->getAfipId() == $this->getParameter('recibo'));
    if(!$tipoRecibo){
      $numeroComprobante = ($this->obtenerUltimoComprobante($afip,$ptoVta,$tipoComprobante->getAfipId()))+1;
    }
    else{
      $ultimoRecibo = $this->manager()->getRepository("App:Comprobante")->findOneBy(['tipoComprobante'=>$tipoComprobante],['numero'=>'DESC']);
      $numeroComprobante = empty($ultimoRecibo) ? 1 : ((int) $ultimoRecibo->getNumero() + 1);
    } 

    /** Venta*/
    $remito = $paramFetcher->get('remito') ?? null;
    $fechaEmision = $paramFetcher->get('fecha_emision');
    $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente')['cliente_id']);
    $tipoVenta = $this->manager()->getRepository("App:TipoVenta")->find($this->getParameter('tipo_venta_comprobante'));//Comprobante
    $venta = new Venta($cliente,$tipoVenta,$fechaEmision);
    $this->manager()->persist($venta);

    /** Estado Venta */
    $condicionVenta = $this->manager()->getRepository("App:CondicionVenta")->find($paramFetcher->get('condicion_vta'));
    if($tipoRecibo && ($condicionVenta->getCondicionVentaId() == $this->getParameter('condicion_cuenta_corriente'))){ //verico si es un recibo y se aplica a cuenta corriente..
      $estado = $this->manager()->getRepository("App:Estado")->findOneBy(['codigo'=>'PENDIENTE']); //obtengo el estado pendiente
      $estadoVenta = new EstadoVenta($venta,$estado);

      /** agrego movimiento cuenta corriente */
      $cuentaCorriente = $cliente->getCuentaCorriente();
      $valor = $paramFetcher->get('importes')['total'];

      $montoCuentaCorriente = $cuentaCorriente->getMonto();
      $cuentaCorriente->setMonto($montoCuentaCorriente + (float)$valor);
      $movimiento = new Movimiento($cuentaCorriente,$valor);
      $this->manager()->persist($cuentaCorriente);
      $this->manager()->persist($movimiento);
    }
    else{
      $estado = $this->manager()->getRepository("App:Estado")->findOneBy(['codigo'=>'PAGADO']); //obtengo el estado pendiente
      $estadoVenta = new EstadoVenta($venta,$estado);
      $this->manager()->persist($estadoVenta);
    }

    /** Productos Ventas **/
    $productos = $paramFetcher->get('productos');
    $this->manager()->getRepository("App:ProductoVenta")->generarProductosVentas($productos,$venta);

    /** Comprobante*/
    $comprobante = new Comprobante($condicionVenta,$venta,$tipoComprobante,$numeroComprobante,$ptoVta,$remito);
    $this->manager()->persist($comprobante);
    return $comprobante;
  }

/** Parametros para la generacion del PDF**/
  private function parametrosComprobantePDF($paramFetcher,$data,$tipoComprobante,$comprobante,$cae=null){
    // dd($cae);
    $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente')['cliente_id']);
    $condicionVenta = $this->manager()->getRepository("App:CondicionVenta")->find($paramFetcher->get('condicion_vta'));
    $data['cliente'] = $cliente;
    $data['productos'] =$paramFetcher->get('productos');
    $data['CbteTipo'] = $tipoComprobante;
    $data['comp'] = $comprobante;
    $data['CbteFch'] 	= new \DateTime($paramFetcher->get('fecha_emision'));
    $data['ImpNeto'] = $paramFetcher->get('importes')['gravado'];
    $data['ImpTotal'] = $paramFetcher->get('importes')['total'];
    $data['CondVta'] = $condicionVenta;
    $data['Iva'] = [];
    if($tipoComprobante->getAfipId() == 1){
      $alicuotas = $this->obtenerAliCuotasTotales($data['productos'],$paramFetcher->get('importes')['gravado']);
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
    // dump($data);
    return $data;
  }
}
