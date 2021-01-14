<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Gonzakpo\AfipBundle\Controller\AfipController;
use Swagger\Annotations as SWG;
use App\Extensions\AfipUtilitiesTrait;
use App\Extensions\PDFUtilitiesTrait;
use App\Extensions\ComprobantesUtilitiesTrait;
use Picqer\Barcode\BarcodeGeneratorPNG;



/**
 * Class ApiController
 *
 * @Route("/api/afip")
 */
class AfipRestController extends RestController
{
    use AfipUtilitiesTrait;
    use PDFUtilitiesTrait;
    use ComprobantesUtilitiesTrait;
    /**
     * @Rest\Get("/estado", name="estado", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve el estado del servidor de AFIP.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar EL estado del servidor de  AFIP")
     * @SWG\Tag(name="Afip")
     */
    public function estadoServidor (AfipController $afip)
    {

        $response = $afip->getWS()->ElectronicBilling->GetServerStatus();
        return $this->apiResponse($response,200);
    }

    /**
     * @Rest\Get("/ultimoComprobante", name="ultimo_comprobante", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve la informacion de un comrprobante.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar la informacion de un comprobante")
     * @SWG\Tag(name="Afip")
     */
    public function ultimoComprobante (AfipController $afip)
    {
        $response = $this->obtenerUltimoComprobante($afip,100, 1);
        return $this->apiResponse(['nro_comprobante'=>$response],$response->code);
    }

    /**
     * @Rest\Get("/comprobante", name="comprobante", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve la informacion de un comrprobante.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar la informacion de un comprobante")
     * @SWG\Tag(name="Afip")
     */
    public function comprobante (Request $request, AfipController $afip)
    {

        // $nroComprobante = $request->query->get('nro');
        // $ptoVenta = $request->query->get('ptoVta');
        // $tipo = $request->query->get('tipo');

        $response = $afip->getWS()->ElectronicBilling->GetVoucherInfo(7,100,1);
        return $this->apiResponse($response,200);
    }


     /**
     * @Rest\Get("/tiposComprobantes", name="tiposComprobantes", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos los tipos de comprobantes.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de comprobantes")
     * @SWG\Tag(name="Afip")
     */
    public function tiposComprobantes(AfipController $afip)
    {
        $response = $afip->getWS()->ElectronicBilling->GetVoucherTypes();
        return $this->apiResponse($response,200);
    }

    /**
    * @Rest\Get("/tiposComprobantesIVA", name="tiposComprobantesIVA", defaults={"_format":"json"})
    * @Rest\QueryParam(name="afip_id",nullable=true)
    * @SWG\Response(response=200,description="Devuelve todos los tipos de comprobantes.")
    * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de comprobantes")
    * @SWG\Tag(name="Afip")
    */
   public function tiposComprobantesPorIVA(ParamFetcher $paramFetcher)
   {
        $condicionIva = $paramFetcher->get('afip_id');
        if(!empty($condicionIva) && $condicionIva == $this->getParameter('responsable_inscripto')){
            $comprobantes[] = $this->getParameter('factura_A'); 
            $comprobantes[] = $this->getParameter('factura_B');
        }
        elseif(!empty($condicionIva)){
            $comprobantes[] =  $this->getParameter('factura_C');
        }
        $comprobantes[] =  $this->getParameter('recibo');
        $tiposComprobantes =  $this->manager()->getRepository("App:TipoComprobante")->tiposComprobantesIva($comprobantes);
        return $this->apiResponse($tiposComprobantes,200);
   }

   

    /**
     * @Rest\Get("/tiposConceptos", name="tiposConceptos", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos los tipos de conceptos.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de conceptos")
     * @SWG\Tag(name="Afip")
     */
    public function tiposConceptos()
    {
        $response =$this->manager()->getRepository("App:TipoConcepto")->findAll();
        return $this->apiResponse($response,200);
    }

    /**
     * @Rest\Get("/condicionesIva", name="condicionesIva", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos los tipos de conceptos.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de conceptos")
     * @SWG\Tag(name="Afip")
     */
    public function condicionesIva()
    {
        $condicionesIva =  $this->manager()->getRepository("App:CondicionIva")->findAll();
        return $this->apiResponse($condicionesIva,200);
    }

    /**
     * @Rest\Get("/tiposDocumentos", name="tiposDocumentos", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos los tipos de documentos.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de documentos")
     * @SWG\Tag(name="Afip")
     */
    public function tiposDocumentos()
    {
        $tipoDocumentos =  $this->manager()->getRepository("App:TipoDocumento")->findAll();
        return $this->apiResponse($tipoDocumentos,200);
    }

    /**
     * @Rest\Get("/instructivo", name="instructivo", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve el instructivo.")
     * @SWG\Tag(name="Afip")
     */
    public function Instructivo()
    {
        $instructivo = file_get_contents($this->getParameter('instructivo_url'));
        $response =  array('file' => "data:application/pdf;base64,".base64_encode($instructivo));
        return $this->apiResponse($response,200);
    }

    /**
     * @Rest\Get("/tiposAliCuotas", name="tiposAliCuotas", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos los tipos de alicuotas.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de alicuotas")
     * @SWG\Tag(name="Afip")
     */
    public function tiposAliCuotas()
    {
        $tiposAliCuotas =  $this->manager()->getRepository("App:TipoAlicuota")->findAll();
        return $this->apiResponse($tiposAliCuotas,200);
    }

    /**
     * @Rest\Get("/tiposMonedas", name="tiposMonedas", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos los tipos de monedas.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de monedas")
     * @SWG\Tag(name="Afip")
     */
    public function tiposMonedas(AfipController $afip)
    {
        $response = $afip->getWS()->ElectronicBilling->GetCurrenciesTypes();
        return $this->apiResponse($response,200);
    }


    /**
     * @Rest\Get("/tiposOpcionesComprobantes", name="tiposOpcionesComprobantes", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos los tipos de opciones de comprobantes.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de opciones de comprobantes")
     * @SWG\Tag(name="Afip")
     */
    public function tiposOpcionesComprobantes (AfipController $afip)
    {
        $response = $afip->getWS()->ElectronicBilling->GetOptionsTypes();
        return $this->apiResponse($response,200);
    }

    /**
     * @Rest\Get("/tiposAtributos", name="tiposAtributos", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos los tipos de atributos.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de atributos")
     * @SWG\Tag(name="Afip")
     */
    public function tiposAtributos (AfipController $afip)
    {
        $response = $afip->getWS()->ElectronicBilling->GetTaxTypes();
        return $this->apiResponse($response,200);
    }

    /**
     * @Rest\Get("/condicionesVenta", name="condicionesVta", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos las condiciones de venta.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de atributos")
     * @SWG\Tag(name="Afip")
     */
    public function condicionesVenta ()
    {
        $condicionesVenta =  $this->manager()->getRepository("App:CondicionVenta")->findAll();
        return $this->apiResponse($condicionesVenta,200);
    }

    /**
     * @Rest\Post("/generarComprobante", name="generar_comprobante", defaults={"_format":"json"})
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
     * @SWG\Response(response=200,description="Devuelve la factura en base 64.")
     * @SWG\Response(response=400,description="Hubo un problema para generar la factura")
     * @SWG\Tag(name="Afip")
     */
     public function generarComprobante (ParamFetcher $paramFetcher, AfipController $afip)
     {
        try{
           /** begin transaccion */
           $this->manager()->getConnection()->beginTransaction();
           $ptoVta = $this->getUser()->getNegocio()->getPuntoVta();
           $tipoComprobante = $this->manager()->getRepository("App:TipoComprobante")->findOneBy(['afipId'=>$paramFetcher->get('comprobante')]);
           $comprobantePreventa = $this->registrarDatosComprobante($paramFetcher,$tipoComprobante,$afip);
           /** obtengo los datos para el comprobante */
           $data = $this->parametrosComprobanteAfip($paramFetcher,$tipoComprobante,$comprobantePreventa->getNumero());


           if($tipoComprobante->getAfipId() != $this->getParameter('recibo')){
             /**Creo el comprobante en AFIP */
             $response = $afip->getWS()->ElectronicBilling->CreateVoucher($data);
             /** Si lo creo correctamente, genero el comprobante pdf y commiteo la transaccion*/
             if(empty($response['CAE'])){
               /** obtengo datos necesarios para la generacion del pdf **/
               throw new Exception('La factura no se generó, debido a un problema en AFIP');
             }
           }
           // dd($response);
           /** genero el pdf con los datos guardados **/
           $data = $this->parametrosComprobantePDF($paramFetcher,$data,$tipoComprobante,$comprobantePreventa,$response ?? NULL);//true por el cpodigo de barras
           $pdfData = $this->generarPdf('pdf/comprobante.html.twig',$data);
           $response =  array('file' => "data:application/pdf;base64,".$pdfData);

           /** persisto los datos*/
           $this->manager()->flush();
           $this->manager()->getConnection()->commit();
           return $this->apiResponse($response,200);

         } catch (\Exception $e) {
             $this->manager()->getConnection()->rollback();
             return $this->apiResponse(['data'=>$e->getMessage()],500);
         }
     }






}
