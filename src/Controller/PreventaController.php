<?php

namespace App\Controller;

use App\Entity\Negocio;
use App\Entity\TipoPreventa;
use App\Entity\Preventa;
use App\Entity\ResponsePreventa;
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
use Swagger\Annotations as SWG;
use App\Extensions\PDFUtilitiesTrait;

/**
 * Class ApiController
 *
 * @Route("/api/preventas")
 */
class PreventaController extends RestController
{
      use PDFUtilitiesTrait;
      /**
      * @Rest\Get("/{preventa}", name="preventa_producto", defaults={"_format":"json"})
      * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
      * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
      * @SWG\Tag(name="Preventa")
      */
     public function preventa(Preventa $preventa)
     {
         try
         {
             $productosPreventa = $this->manager()->getRepository("App:ProductoPreventa")->findBy(['preventa'=>$preventa]);
             $response = ['preventa'=>$preventa,
                          'productos'=>$productosPreventa];
             return $this->apiResponse($response,200);
         } catch (Exception $e)
         {
             return $this->apiResponse($ex->getMessage(),500);
         }
     }

     /**
     * @Rest\Get("/descargar/{preventa}", name="descargar_preventa_producto", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
     * @SWG\Tag(name="Preventa")
     */
    public function descargarPreventa(Preventa $preventa)
    {
        try
        {
            $productosPreventa = $this->manager()->getRepository("App:ProductoPreventa")->findBy(['preventa'=>$preventa]);
            $data = ['preventa'=>$preventa,
                      'productos'=>$productosPreventa];
            $pdfData = $this->generarPdf('pdf/pedido.html.twig',$data);
            $response =  array('file' => "data:application/pdf;base64,".$pdfData);
            return $this->apiResponse($response,200);
        } catch (Exception $e)
        {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

     /**
     * @Rest\Get("/negocio/{negocio}", name="lista_pedidos", defaults={"_format":"json"})
     * @Rest\QueryParam(name="tipo",nullable=false)
     * @Rest\QueryParam(name="estado",nullable=true)
     * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
     * @SWG\Tag(name="Preventa")
     */
    public function pedidosNegocio(ParamFetcher $paramFetcher,Negocio $negocio)
    {
        try
        {

            $tipoPreventa = $this->manager()->getRepository("App:TipoPreventa")->find($paramFetcher->get('tipo'));
            $estado = !empty($paramFetcher->get('estado')) ?  $this->manager()->getRepository("App:Estado")->find($paramFetcher->get('estado')) : null;
            $pedidos = $this->manager()->getRepository("App:Preventa")->preventasTipo($negocio,$tipoPreventa,$estado);

            /** obtengo las cantidades  */
            $dataTotales = $this->manager()->getRepository("App:Preventa")->totalesPorEstado($negocio);
            $totales['total'] = 0;
            $totales['pendiente'] = 0;
            $totales['realizado'] = 0;
            $totales['cancelado'] = 0;

            foreach ($dataTotales as $value) {
              $totales[strtolower($value['descripcion'])] =  (int)$value['total'];
              $totales['total'] += $value['total'];
            }
            $response = ['pedidos'=>$pedidos,'totales'=>$totales];
            // return $this->apiResponse($estado,200);
            return $this->apiResponse($response,200);
        } catch (Exception $e)
        {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Post("/negocio/{negocio}/nuevo", name="nueva_preventa", defaults={"_format":"json"})
     * @Rest\RequestParam(name="cliente",nullable=false)
     * @Rest\RequestParam(name="productos",nullable=false)
     * @Rest\RequestParam(name="fecha",nullable=false)
     * @SWG\Response(response=201,description="Producto creado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la marca")
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="nombre del producto",schema={})

     * @SWG\Tag(name="Preventa")
     */
    public function nuevaPreventa(ParamFetcher $paramFetcher, Negocio $negocio)
    {
        try {
            $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente')['cliente_id']);
            $fecha = $paramFetcher->get('fecha');

            /** verifico si en esa fecha ya se encuentra un pedido para ese cliente */
            $pedidoCliente = $this->manager()->getRepository("App:Preventa")->findOneBy(['cliente'=>$cliente,
                                                                                         'fecha'=>new \DateTime($fecha)]);
            if(!empty($pedidoCliente)) {
              throw new Exception('Ya existe un pedido para el cliente en esa fecha');
            }
            $tipoPreventa = $this->manager()->getRepository("App:TipoPreventa")->find(2);//tipo pedido
            $preventa = new Preventa($cliente,$tipoPreventa,$fecha);
            $this->manager()->persist($preventa);

            /** Productos Preventas **/
            $productos = $paramFetcher->get('productos');
            $this->manager()->getRepository("App:ProductoPreventa")->generarProductosPreventas($productos,$preventa);

            /** Comprobante Preventa*/
            $estadoPendiente =  $this->manager()->getRepository("App:Estado")->find(1);//pendiente
            $comprobantePreventa = new ComprobantePreventa($preventa,$estadoPendiente,'S');
            $this->manager()->persist($comprobantePreventa);
            /** una vez creado el producto preventa se deberia notificar*/
            $this->manager()->flush();
            return $this->apiResponse($comprobantePreventa,201);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/preventa/{preventa}/editar", name="editar_preventa", defaults={"_format":"json"})
     * @Rest\RequestParam(name="cliente",nullable=false)
     * @Rest\RequestParam(name="productos",nullable=false)
     * @Rest\RequestParam(name="fecha",nullable=false)
     * @SWG\Response(response=201,description="Producto creado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la marca")
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="nombre del producto",schema={})

     * @SWG\Tag(name="Preventa")
     */
    public function editarPreventa(ParamFetcher $paramFetcher, Preventa $preventa)
    {
        try {
            $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente')['cliente_id']);
            $fecha = $paramFetcher->get('fecha');
            $productos = $paramFetcher->get('productos');
            $preventa->setCliente($cliente);
            $preventa->setFecha(new \DateTime($fecha));
            $this->manager()->getRepository("App:ProductoPreventa")->editarProductosPreventas($productos,$preventa);

            $this->manager()->flush();
            return $this->apiResponse($preventa,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/cambiarEstado/{preventa}", name="eliminar_preventa", defaults={"_format":"json"})
     * @Rest\RequestParam(name="estado",nullable=false)
     * @SWG\Response(response=200,description="Eliminado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la marca")
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="nombre del producto",schema={})

     * @SWG\Tag(name="Preventa")
     */
    public function cambiarEstadoPreventa(ParamFetcher $paramFetcher,Preventa $preventa)
    {
        try {
            /** cambio la vigencia de la ultima preventa */
            $comprobantePreventa = $this->manager()->getRepository("App:ComprobantePreventa")->findOneBy(['preventa'=>$preventa,'vigente'=>'S']);
            $comprobantePreventa->setVigente('N');

            $productosPreventa =   $this->manager()->getRepository("App:ProductoPreventa")->findBy(['preventa'=>$preventa]);
            /** obtengo el  estado*/
            $estado =  $this->manager()->getRepository("App:Estado")->find($paramFetcher->get('estado'));//pendiente
            $nuevoComprobantePreventa = new ComprobantePreventa($preventa,$estado,'S');
            $this->manager()->persist($nuevoComprobantePreventa);

            if($estado->getEstadoId() == $this->getParameter('estado_cancelado')){
              /** restauro el stock */
              $this->manager()->getRepository("App:ProductoPreventa")->restablecerProductosPreventas($productosPreventa,$preventa);
            }
            $response = new ResponsePreventa($preventa->getPreventaId(),$preventa->getCliente()->getRazonSocial(),$preventa->getFecha(),$estado->getDescripcion());
            $this->manager()->flush();
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }



}
