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
    $ptoVta = $this->getUser()->getNegocio()->getPuntoVta();
    $tipoRecibo = ($tipoComprobante->getAfipId() == $this->getParameter('recibo'));
    $numeroComprobante = null;
    if(!$tipoRecibo){
      $numeroComprobante = ($this->obtenerUltimoComprobante($afip,$ptoVta,$tipoComprobante->getAfipId()))+1;
    }
    
    /** Venta*/
    $remito = $paramFetcher->get('remito') ?? null;
    $fechaEmision = new \Datetime($paramFetcher->get('fecha_emision'));
    $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente'));
    $tipoVenta = $this->manager()->getRepository("App:TipoVenta")->find($this->getParameter('tipo_venta_comprobante'));//Comprobante
    $paramVenta = $paramFetcher->get('venta') ?? null;
    /** sino existe la venta */
    if(!$paramVenta){
      $venta = new Venta($cliente,$tipoVenta,$fechaEmision);
      $this->manager()->persist($venta);
    }
    else{
      $venta = $this->manager()->getRepository("App:Venta")->find($paramVenta);
      $estadoVentaAnt = $this->manager()->getRepository("App:EstadoVenta")->findOneBy(['vigente'=>'S','venta'=>$venta]);
      $estadoVentaAnt->setVigente('N');
    }

    /** Estado Venta */
    $condicionVenta = $this->manager()->getRepository("App:CondicionVenta")->find($paramFetcher->get('condicion_vta'));
    if($tipoRecibo && ($condicionVenta->getCondicionVentaId() == $this->getParameter('condicion_cuenta_corriente'))){ //verico si es un recibo y se aplica a cuenta corriente..
      $venta->setMontoDebido($paramFetcher->get('importes')['total']);
      $venta->setFModificacion( new \DateTime());
      $estado = $this->manager()->getRepository("App:Estado")->findOneBy(['codigo'=>'PENDIENTEPAGO']); //obtengo el estado pendiente de pago
      $estadoVenta = new EstadoVenta($venta,$estado);
      $this->manager()->persist($estadoVenta);
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
    $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente'));
    foreach ($paramFetcher->get('productos') as $value) {
      $value['producto'] = $this->manager()->getRepository("App:Producto")->find($value['producto']);
      if($tipoComprobante->getAfipId()  == $this->getParameter('factura_A')){
        $value['tipo_alicuota'] = $this->manager()->getRepository("App:TipoAlicuota")->find($value['tipo_alicuota']);
      }
      $productos[] = $value;
    }
    $condicionVenta = $this->manager()->getRepository("App:CondicionVenta")->find($paramFetcher->get('condicion_vta'));
    $data['cliente'] = $cliente;
    $data['productos'] =$productos;
    $data['CbteTipo'] = $tipoComprobante;
    $data['comp'] = $comprobante;
    $data['CbteFch'] 	= new \DateTime($paramFetcher->get('fecha_emision'));
    $data['ImpNeto'] = $paramFetcher->get('importes')['gravado'];
    $data['ImpTotal'] = $paramFetcher->get('importes')['total'];
    $data['CondVta'] = $condicionVenta;
    $data['Iva'] = [];
    if($tipoComprobante->getAfipId() == $this->getParameter('factura_A') ){
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
