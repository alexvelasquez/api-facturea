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
    * @Rest\Get("", name="lista_comprobantes", defaults={"_format":"json"})
    * @Rest\QueryParam(name="fechaDesde",nullable=false)
    * @Rest\QueryParam(name="fechaHasta",nullable=false)
    * @SWG\Response(response=200,description="Devuelve todos los comprobantes de un negocio.")
    * @SWG\Response(response=500,description="Hubo un problema para recuperar los comprobates de un negocio")
    * @SWG\Tag(name="Marca")
    */
   public function comprobantes(ParamFetcher $paramFetcher)
   {
       try
       {
            $negocio = $this->getUser()->getNegocio();
            $fechaDesde = $paramFetcher->get('fechaDesde');
            $fechaHasta = $paramFetcher->get('fechaHasta');
            $response = $this->manager()->getRepository("App:Comprobante")->comprobantesPorFechas($negocio,$fechaDesde,$fechaHasta);
            return $this->apiResponse($response,200);
       } catch (Exception $e)
       {
           return $this->apiResponse($e->getMessage(),500);
       }
   }

}
