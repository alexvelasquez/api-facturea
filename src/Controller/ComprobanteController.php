<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Negocio;
use App\Entity\Preventa;
use App\Entity\ComprobantePreventa;
use App\Entity\CuentaCorriente;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
use Knp\Bundle\SnappyBundle\Snappy\Response\PdfResponse;
use Swagger\Annotations as SWG;
use Gonzakpo\AfipBundle\Controller\AfipController;
use App\Extensions\AfipUtilitiesTrait;
use App\Extensions\PDFUtilitiesTrait;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
 * Class ApiController
 *
 * @Route("/api/comprobantes")
 */
class ComprobanteController extends RestController
{

    use AfipUtilitiesTrait;
    use PDFUtilitiesTrait;

    /**
    * @Rest\Get("/negocio/{negocio}", name="lista_comprobantes", defaults={"_format":"json"})
    * @Rest\QueryParam(name="fechaDesde",nullable=false)
    * @Rest\QueryParam(name="fechaHasta",nullable=false)
    * @SWG\Response(response=200,description="Devuelve todos los comprobantes de un negocio.")
    * @SWG\Response(response=500,description="Hubo un problema para recuperar los comprobates de un negocio")
    * @SWG\Tag(name="Marca")
    */
   public function comprobantes(ParamFetcher $paramFetcher, Negocio $negocio)
   {
       try
       {
           $fechaDesde = $paramFetcher->get('fechaDesde').' 00:00:00';
           $fechaHasta = $paramFetcher->get('fechaHasta').' 23:59:59';
           $response = $this->manager()->getRepository("App:ComprobantePreventa")->comprobantes($negocio,$fechaDesde,$fechaHasta);
           return $this->apiResponse($response,200);
       } catch (Exception $e)
       {
           return $this->apiResponse($ex->getMessage(),500);
       }
   }


    /**
     * @Rest\Post("/generar", name="generar_comprobante", defaults={"_format":"json"})
     * @Rest\RequestParam(name="cliente",nullable=false)
     * @Rest\RequestParam(name="comprobante",nullable=false)
     * @Rest\RequestParam(name="concepto",nullable=false)
     * @Rest\RequestParam(name="fecha_emision",nullable=false)
     * @Rest\RequestParam(name="fecha_desde",nullable=true)
     * @Rest\RequestParam(name="fecha_hasta",nullable=true)
     * @Rest\RequestParam(name="fecha_vto",nullable=true)
     * @Rest\RequestParam(name="productos",nullable=false)
     * @Rest\RequestParam(name="condicion_vta",nullable=false)
     * @Rest\RequestParam(name="importes",nullable=false)
     * @SWG\Response(response=200,description="Devuelve la informacion de un comrprobante.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar la informacion de un comprobante")
     * @SWG\Tag(name="Comprobante")
     */
    public function generarComprobante (ParamFetcher $paramFetcher, AfipController $afip)
    {
          try{
          /** begin transaccion */
          $this->manager()->getConnection()->beginTransaction();

          $esRecibo = $this->getParameter('tipo_comprobante_recibo_afip') == $paramFetcher->get('comprobante');
          /**Creo el comprobante */
          $tipoComprobante = $this->manager()->getRepository("App:TipoComprobante")->findOneBy(['afipId'=>$paramFetcher->get('comprobante')]);
          /** verifico si es recibo y obtengo el numero del recibo**/
          $ultimoRecibo = $esRecibo ? $this->manager()->getRepository("App:ComprobantePreventa")->findOneBy(['vigente'=>'S','tipoComprobante'=>$tipoComprobante]) : null;
          $nroRecibo =!empty($ultimoRecibo) ? $ultimoRecibo->getNumero() : 0;

          $ptoVta = $paramFetcher->get('cliente')['negocio']['punto_vta'];
          $nroComprobante = $esRecibo ? ($nroRecibo + 1) : ($this->obtenerUltimoComprobante($afip, $ptoVta, $tipoComprobante->getAfipId())) + 1;

          /** Preventa*/
          $fechaEmision = $paramFetcher->get('fecha_emision');
          $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente'));
          $tipoPreventa = $this->manager()->getRepository("App:TipoPreventa")->find(1);//Comprobante
          $preventa = new Preventa($cliente,$tipoPreventa,$fechaEmision);
          $this->manager()->persist($preventa);

          /** Productos Preventas **/
          $productos = $paramFetcher->get('productos');
          $this->manager()->getRepository("App:ProductoPreventa")->generarProductosPreventas($productos,$preventa);

          /** Comprobante Preventa*/
          $condicionVenta = $this->manager()->getRepository("App:CondicionVenta")->find($paramFetcher->get('condicion_vta'));
          $estadoComprobante = $this->getParameter('estado_pagado');
          $importes = $paramFetcher->get('importes');
          if($condicionVenta->getCondicionVentaId() == $this->getParameter('cuenta_corriente') && $esRecibo){ //verico si es un recibo y se aplica a cuenta corriente..
            $estadoComprobante = $this->getParameter('estado_pendiente_pago');
            $preventa->setMontoDebido($importes['total']);
          }
          $estadoPagado =  $this->manager()->getRepository("App:Estado")->find($estadoComprobante);//pagado o pendiente_pago
          $comprobantePreventa = new ComprobantePreventa($preventa,$estadoPagado,'S',$tipoComprobante,$condicionVenta,$nroComprobante,$ptoVta);
          $this->manager()->persist($comprobantePreventa);

          /** obtengo datos para comprobante AFIP*/
          $concepto = $paramFetcher->get('concepto');
          $tipoDoc = $cliente->getTipoDocumento()->getAfipId();
          $nroDoc = ($tipoDoc != 6) ? $cliente->getDocumento() : 0;

          /** datos opcionales para las facturas con concepto servicios o produtos y servicios*/
          $fechaDesde =   !empty($paramFetcher->get('fecha_desde')) ?  intval(date('Ymd',strtotime($paramFetcher->get('fecha_desde')))) : NULL;
          $fechaHasta =   !empty($paramFetcher->get('fecha_hasta')) ?  intval(date('Ymd',strtotime($paramFetcher->get('fecha_hasta')))) : NULL;
          $fechaVto   =   !empty($paramFetcher->get('fecha_vto')) ?  intval(date('Ymd',strtotime($paramFetcher->get('fecha_vto')))) : NULL;


            /** si el usuario esta habilitado para factura electronica y es un comprobante valido**/
          if(!empty($this->getUser()->getFacturaElectronica()) && !$esRecibo){
              /** Creo el comprobante en la AFIP*/
              $data = array(
                'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
                'PtoVta' 	=> $ptoVta,  // Punto de venta
                'CbteTipo' 	=> intval($tipoComprobante->getAfipId()),  // Tipo de comprobante (ver tipos disponibles)
                'Concepto' 	=> $concepto,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
                'DocTipo' 	=> $tipoDoc, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
                'DocNro' 	=> $nroDoc,  // Número de documento del comprador (0 consumidor final)
                'CbteDesde' 	=> $nroComprobante,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
                'CbteHasta' 	=> $nroComprobante,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
                'CbteFch' 	=>  intval(date('Ymd',strtotime($fechaEmision))), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
                'ImpTotal' 	=> $importes['total'], // Importe total del comprobante
                'ImpTotConc' 	=> $importes['noGravado'],   // Importe neto no gravado
                'ImpNeto' 	=> $importes['gravado'], // Importe neto gravado
                'ImpOpEx' 	=> $importes['exento'],   // Importe exento de IVA
                'ImpIVA' 	=> $importes['iva'],  //Importe total de IVA
                'ImpTrib' 	=> 0,   //Importe total de tributos
                'FchServDesde' 	=> $fechaDesde , // (Opcional) Fecha de inicio del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                'FchServHasta' 	=> $fechaHasta, // (Opcional) Fecha de fin del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                'FchVtoPago' 	=> $fechaVto, // (Opcional) Fecha de vencimiento del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
                'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos)
                'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)
              );

              /** Verifico si es una FACTURA A y agrego los datos adicionales*/
              if($tipoComprobante->getAfipId() == 1){
                  $alicuotas = $this->obtenerAliCuotasTotales($productos,$importes['gravado']);
                  $data['Iva'] = array_values($alicuotas);
              }
              /**Creo el comprobante en AFIP */
              $response = $afip->getWS()->ElectronicBilling->CreateVoucher($data);
              /** Si lo creo correctamente, genero el comprobante pdf y commiteo la transaccion*/
              if($response['CAE']){
                $data['cliente'] = $cliente;
                $data['productos'] = $productos;
                $data['CbteTipo'] = $tipoComprobante;
                $data['cae'] = $response;
                $data['compPrev'] = $comprobantePreventa;
                $data['Iva'] = $alicuotas ?? [];
                $data['CondVta'] = $condicionVenta;

                /** obtengo el codigo de barras formado por cuitcuil-tipoCbte-**/
                $digitoVerificador = substr($cliente->getNegocio()->getCuitCuil(), -1);
                $codigo = $cliente->getNegocio()->getCuitCuil().$data['CbteTipo']->getAfipId().$data['PtoVta'].$response['CAE'].$digitoVerificador;
                $generator = new BarcodeGeneratorPNG();
                $codigoBarras = 'data:image/png;base64,'.base64_encode($generator->getBarcode($codigo, $generator::TYPE_CODE_128));
                $data['codigo'] = $codigo;
                $data['codigoBarras'] = $codigoBarras;

                /** genero el pdf con los datos guardados **/
                $pdfData = $this->generarPdf('pdf/comprobante.html.twig',$data);
                $response =  array('file' => "data:application/pdf;base64,".$pdfData);

              }
              else{
                throw new Exception('La factura no se generó, debido a un problema en AFIP');
              }
          }
          else{
            $data['cliente'] = $cliente;
            $data['productos'] = $productos;
            $data['CbteTipo'] = $tipoComprobante;
            $data['compPrev'] = $comprobantePreventa;
            $data['CbteFch'] 	= intval(date('Ymd',strtotime($fechaEmision)));
            $data['Iva'] = [];
            $data['ImpNeto'] 	= $importes['gravado'];
            $data['ImpTotal'] 	= $importes['total'];
            $data['CondVta'] = $condicionVenta;
            /** genero el pdf con los datos guardados **/
            $pdfData = $this->generarPdf('pdf/comprobante.html.twig',$data);
            $response =  array('file' => "data:application/pdf;base64,".$pdfData);
          }

          $this->manager()->flush();
          $this->manager()->getConnection()->commit();
          return $this->apiResponse($response,200);

        } catch (\Exception $e) {
            $this->manager()->getConnection()->rollback();
            return $this->apiResponse(['data'=>$e->getMessage()],500);
        }
    }



}
