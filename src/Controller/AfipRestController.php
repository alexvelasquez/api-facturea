<?php

namespace App\Controller;

// use App\Entity\Player;
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
use Gonzakpo\AfipBundle\Controller\AfipController;
use Swagger\Annotations as SWG;
use App\Extensions\AfipUtilitiesTrait;



/**
 * Class ApiController
 *
 * @Route("/api/afip")
 */
class AfipRestController extends RestController
{
    use AfipUtilitiesTrait;
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
        $tiposComprobantes =  $this->manager()->getRepository("App:TipoComprobante")->tiposComprobantesIva($condicionIva);
        return $this->apiResponse($tiposComprobantes,200);
   }


    /**
     * @Rest\Get("/tiposConceptos", name="tiposConceptos", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos los tipos de conceptos.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de conceptos")
     * @SWG\Tag(name="Afip")
     */
    public function tiposConceptos(AfipController $afip)
    {
        $response = $afip->getWS()->ElectronicBilling->GetConceptTypes();
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
        $instructivo = file_get_contents($this->getParameter('public_directory').'/doc/Instructivo.pdf');
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
        $tiposAliCuotas =  $this->manager()->getRepository("App:TipoAliCuota")->findAll();
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
     * @SWG\Response(response=200,description="Devuelve todos los tipos de atributos.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar los tipos de atributos")
     * @SWG\Tag(name="Afip")
     */
    public function condicionesVenta ()
    {
        $condicionesVenta =  $this->manager()->getRepository("App:CondicionVenta")->findAll();
        return $this->apiResponse($condicionesVenta,200);
    }

    /** funciones adicionales **/



}
