<?php
namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Negocio;
use App\Entity\Marca;
use App\Entity\Categoria;
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
 * @Route("/api/marcas")
 */
class MarcasController extends RestController
{

    use PDFUtilitiesTrait;
     /**
     * @Rest\Get("/negocio/{negocio}", name="lista_marca", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
     * @SWG\Tag(name="Marca")
     */
    public function marcasNegocio( Negocio $negocio)
    {
        try
        {
            $response = $this->manager()->getRepository("App:Marca")->findBy(array('negocio'=> $negocio), array('descripcion' => 'ASC'));
            return $this->apiResponse($response,200);
        } catch (Exception $e)
        {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Post("/nuevo", name="nueva_marca", defaults={"_format":"json"})
     * @Rest\RequestParam(name="descripcion",nullable=false)
     * @SWG\Response(response=201,description="Producto creado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la marca")
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="nombre del producto",schema={})

     * @SWG\Tag(name="Marca")
     */
    public function nuevaMarca(ParamFetcher $paramFetcher)
    {
        try {
            $descripcion = $paramFetcher->get('descripcion');
            $negocio = $this->getUser()->getNegocio();
            $codigo = count($this->manager()->getRepository("App:Marca")->findBy(['negocio'=>$negocio])) + 1;
            $marca = new Marca($descripcion,$codigo,$negocio);
            $this->manager()->persist($marca);
            $this->manager()->flush();

            return $this->apiResponse($marca,201);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/editar/{marca}", name="editar_marca", defaults={"_format":"json"})
     * @Rest\RequestParam(name="descripcion",nullable=false)
     * @SWG\Response(response=200,description="Actualiza la marca de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     *
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="descripcion de la marca",schema={})
     * @SWG\Tag(name="Marca")
     */
    public function editarMarca(ParamFetcher $paramFetcher, Marca $marca )
    {
        try {
            $errores = [];
            $descripcion = $paramFetcher->get('descripcion');
            /** Actualizo los campos del producto */
            $marca->setDescripcion($descripcion);
            $this->manager()->flush();

            return $this->apiResponse($marca,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/eliminar/{marca}", name="eliminar_marca", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza la marca de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     *
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="descripcion de la marca",schema={})
     * @SWG\Tag(name="Marca")
     */
    public function eliminarMarca(Marca $marca)
    {
        try {
            $errores = [];
            $this->manager()->remove($marca);
            $this->manager()->flush();

            return $this->apiResponse($marca,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/eliminarMarcas", name="eliminar_marcas", defaults={"_format":"json"})
     * @Rest\RequestParam(name="marcas",nullable=false)
     * @SWG\Response(response=200,description="Elimina las marcas de  un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Producto")
     */
    public function eliminarMarcas(ParamFetcher $paramFetcher)
    {
        try
        {
          $marcas = json_decode($paramFetcher->get('marcas'));
          /** begin transaccion */
          $this->manager()->getConnection()->beginTransaction();
          foreach ($marcas as $m)
          {
              $marca = $this->manager()->getRepository("App:Marca")->find($m->marca_id);
              /** Actualizo los campos del producto */
              $marca->setFModificacion(new \DateTime());
              $marca->setFHasta(new \DateTime());
              $this->manager()->flush();
          }
          /** end transaccion */
          $this->manager()->getConnection()->commit();
          return $this->apiResponse([],200);

        } catch (Exception $e) {
          /** rollback transaccion */
          $this->manager()->getConnection()->rollback();
          return $this->apiResponse($ex->getMessage(),500);
        }
    }

     /**
     * @Rest\Put("/incremento", name="incremento_marca", defaults={"_format":"json"})
     * @Rest\RequestParam(name="marca",nullable=true)
     * @Rest\RequestParam(name="incremento",nullable=true)
     * @Rest\RequestParam(name="cuentaCorriente",nullable=true)
     * @SWG\Response(response=200,description="Incrementa el porcentaje recibido a todos los productos por marca.")
     * @SWG\Response(response=500,description="Hubo un problema al incrementar los productos por marca")
     * @SWG\Tag(name="Producto")
     */
    public function incrementoPorMarca(ParamFetcher $paramFetcher)
    {
        try
        {
            $estadoPendientePago = $this->getParameter('estado_pendiente_pago');
            $marca = $this->manager()->getRepository("App:Marca")->find($paramFetcher->get('marca'));
            $incremento = $paramFetcher->get('incremento');
            $cuentaCorriente = !empty($paramFetcher->get('cuentaCorriente')) ? $paramFetcher->get('cuentaCorriente') : null;
            /** inicio transaccion */
            $this->manager()->getConnection()->beginTransaction();
            $productos = $this->manager()->getRepository("App:Producto")->findBy(['marca'=>$marca]);
            /** actualizo los montos del producto */
            foreach ($productos as $producto)
            {
                $producto->setPrecioCompra($producto->getPrecioPublicado());
                $producto->setAumento($incremento);
                $producto->setFModificacion(new \DateTime());
                $this->manager()->flush();
            }

            if(!empty($cuentaCorriente)){
              /** obtengo todos los productos preventa con estado pendiente de pago de la marca **/
              $productosPreventasMarcas = $this->manager()->getRepository("App:ProductoPreventa")->productosPreventaTiposProd($marca,$estadoPendientePago);
              /** modifico el subtotal y precio unitario**/
              foreach ($productosPreventasMarcas as $value) {
                  $productoPreventa = $this->manager()->getRepository("App:ProductoPreventa")->find($value['productoPreventaId']);
                  $precioUnitario = $productoPreventa->getProducto()->getPrecioPublicado();
                  $precioCantidad =  $precioUnitario * $productoPreventa->getCantidad();
                  $montoBonificado =  $precioCantidad * ($productoPreventa->getMontoBonif()/100);
                  $subtotalSinIva = $precioCantidad - $montoBonificado;
                  $valorTipoAlicuota = !empty($productoPreventa->getTipoAliCuota()) ? ( $valor->getTipoAliCuota()->getValor() / 100 ) : 0;
                  $montoIva = $subtotalSinIva * $valorTipoAlicuota;
                  $subtotal = $subtotalSinIva + $montoIva;

                  $productoPreventa->setSubtotal($subtotal);
                  $productoPreventa->setPrecioUnitario($precioUnitario);
                  $productoPreventa->setMontoIva($montoIva);
                  $productoPreventa->setMontoBonif($montoBonificado);
                  $productoPreventa->setSubtotalSinIva($subtotalSinIva);
                  $this->manager()->flush();
              }
            }
            /** commit transaccion */
            $this->manager()->getConnection()->commit();
            return $this->apiResponse([],200);
        }catch (Exception $e)
        {
            /** rollback transaccion */
            $this->manager()->getConnection()->rollback();
            return $this->apiResponse($ex->getMessage(),500);
        }
    }


      /**
      * @Rest\Get("/exportar/{negocio}", name="descargar_marcas", defaults={"_format":"json"})
      * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
      * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
      * @SWG\Tag(name="Cliente")
      */
     public function exportarMarcas(Negocio $negocio)
     {
         try
         {
             $marcas = $this->manager()->getRepository("App:Marca")->findBy(array('negocio'=> $negocio), array('descripcion' => 'ASC'));
             $data = ['titulo'=>'MARCAS','datos'=>$marcas];
             $pdfData = $this->generarPdf('pdf/marcasCategorias.html.twig',$data);
             $response =  array('file' => "data:application/pdf;base64,".$pdfData);
             return $this->apiResponse($response,200);
         } catch (Exception $e)
         {
             return $this->apiResponse($ex->getMessage(),500);
         }
     }


}
