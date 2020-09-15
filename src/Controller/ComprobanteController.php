<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Negocio;
use App\Entity\ComprobantePreventa;
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
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Extensions\AfipUtilitiesTrait;
/**
 * Class ApiController
 *
 * @Route("/api/comprobantes")
 */
class ComprobanteController extends RestController
{

    use AfipUtilitiesTrait;
     /**
     * @Rest\Get("/negocio/{negocio}", name="lista_comprobantes", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas los clientes de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los clientes de un negocio")
     * @SWG\Tag(name="Comprobante")
     */
    public function clientesNegocio( Negocio $negocio)
    {
        try{
            $response = $this->manager()->getRepository("App:Comprobante")->findBy(['negocio'=>$negocio]);
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
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
     * @Rest\RequestParam(name="negocio",nullable=false)
     * @Rest\RequestParam(name="productos",nullable=false)
     * @Rest\RequestParam(name="total",nullable=false)
     * @SWG\Response(response=200,description="Devuelve la informacion de un comrprobante.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar la informacion de un comprobante")
     * @SWG\Tag(name="Comprobante")
     */
    public function generarComprobante (ParamFetcher $paramFetcher, AfipController $afip)
    {
        try {
          /** begin transaccion */
          $this->manager()->getConnection()->beginTransaction();

          $ptoVta = $paramFetcher->get('negocio')['punto_vta'];
          $tipoComprobante = $paramFetcher->get('comprobante');
          /** obtengo el ultimo comprobante creado **/
          $ultimoComprobante = $this->obtenerUltimoComprobante($afip, $ptoVta, $tipoComprobante);
          /** obtengo los datos*/
          $concepto = $paramFetcher->get('concepto');
          $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente')['cliente_id']);
          $tipoDoc = $cliente->getTipoDocumento()->getTipoDocumentoId();
          $nroDoc = ($tipoDoc != 6) ? $cliente->getDocumento() : 0;
          $comprobante  = $ultimoComprobante + 1;
          $fechaEmision = $paramFetcher->get('fecha_emision');
          /** datos opcionales para las facturas con concepto servicios o produtos y servicios*/
          $fechaDesde =   !empty($paramFetcher->get('fecha_desde')) ?  intval(date('Ymd',strtotime($paramFetcher->get('fecha_desde')))) : NULL;
          $fechaHasta =   !empty($paramFetcher->get('fecha_hasta')) ?  intval(date('Ymd',strtotime($paramFetcher->get('fecha_hasta')))) : NULL;
          $fechaVto   =   !empty($paramFetcher->get('fecha_vto')) ?  intval(date('Ymd',strtotime($paramFetcher->get('fecha_vto')))) : NULL;
          /** obtengo los montos correspondientes */
          $importeTotal = $paramFetcher->get('total');
          $productosFactura = $paramFetcher->get('productos');
          $importes = $this->obtenerMontosTotalesFactura($productosFactura,$tipoComprobante);

          /**Creo la preventa, tabla de registro de la emision del comprobante */
          /** Obtener el ultimo comprobante creado y cambiarle la vigencia */
          $comprobantePreventa = new ComprobantePreventa($cliente,$importeTotal,new \DateTime($fechaEmision),'S');
          $this->manager()->getRepository("App:ProductoComprobantePreventa")->generarProductosComprobantesPreventas($productosFactura,$comprobantePreventa);

          // // - Creo comporbante pedido (ESTADO, TipoComprobante, comporbante(si es factura))
          // $data = array(
          // 	'CantReg' 	=> 1,  // Cantidad de comprobantes a registrar
          // 	'PtoVta' 	=> $ptoVta,  // Punto de venta
          // 	'CbteTipo' 	=> $tipoComprobante,  // Tipo de comprobante (ver tipos disponibles)
          // 	'Concepto' 	=> $concepto,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
          // 	'DocTipo' 	=> $tipoDoc, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
          // 	'DocNro' 	=> $nroDoc,  // Número de documento del comprador (0 consumidor final)
          // 	'CbteDesde' 	=> $comprobante,  // Número de comprobante o numero del primer comprobante en caso de ser mas de uno
          // 	'CbteHasta' 	=> $comprobante,  // Número de comprobante o numero del último comprobante en caso de ser mas de uno
          // 	'CbteFch' 	=>  intval(date('Ymd',strtotime($fechaEmision))), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
          // 	'ImpTotal' 	=> $importeTotal, // Importe total del comprobante
          // 	'ImpTotConc' 	=> $importes['netoNoGravado'],   // Importe neto no gravado
          // 	'ImpNeto' 	=> $importes['netoGravado'], // Importe neto gravado
          // 	'ImpOpEx' 	=> $importes['exento'],   // Importe exento de IVA
          // 	'ImpIVA' 	=> $importes['IVA']['total'],  //Importe total de IVA
          // 	'ImpTrib' 	=> 0,   //Importe total de tributos
          //   'FchServDesde' 	=> $fechaDesde , // (Opcional) Fecha de inicio del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
          //   'FchServHasta' 	=> $fechaHasta, // (Opcional) Fecha de fin del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
          //   'FchVtoPago' 	=> $fechaVto, // (Opcional) Fecha de vencimiento del servicio (yyyymmdd), obligatorio para Concepto 2 y 3
          //   'MonId' 	=> 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos)
          //   'MonCotiz' 	=> 1,     // Cotización de la moneda usada (1 para pesos argentinos)
          // );
          // Verifico si es una FACTURA A y agrego los datos adicionales
          // if($tipoComprobante == 1){
          //       $data['Iva'] = $importes['IVA']['alicuotas'];
          // }
          // $response = $afip->getWS()->ElectronicBilling->CreateVoucher($data);
            $this->manager()->getConnection()->commit();
            return $this->apiResponse(['data'=>'Agregado'],200);
        } catch (\Exception $e) {
            $this->manager()->getConnection()->rollback();
            return $this->apiResponse(['data'=>$e->getMessage()],500);
        }




    }

    /**
     * @Rest\get("/generatePDF", name="generate_pdf", defaults={"_format":"json"})
     * @SWG\Tag(name="Comprobante")
     */
    public function comprobantePDF()
    {

      $html = $this->renderView('pdf/factura.html.twig',['url'=>'.build\logo_afip.jpg']);

      //dd($this->getParameter('public_directory').'\uploads\c81e728d9d4c2f636f067f89cc14862c.jpg');
      $options = new Options();
      $options->set('isRemoteEnabled', TRUE);
      $options->setIsHtml5ParserEnabled(true);
      $dompdf = new Dompdf($options);
      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->output();
      $dompdf->render();
      // dd(base64_encode($dompdf->render()));
      $dompdf->stream("testpdf.pdf", [
          "Attachment" => false ]);

    }



}
