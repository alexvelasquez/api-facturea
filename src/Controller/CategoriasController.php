<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Negocio;
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
 * @Route("/api/categorias")
 */
class CategoriasController extends RestController
{
    use PDFUtilitiesTrait;
     /**
     * @Rest\Get("/negocio/{negocio}", name="lista_categorias", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas las categorias")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar las categorias")
     * @SWG\Tag(name="Categoria")
     */
    public function categoriasNegocio(Negocio $negocio)
    {
        try{
            $response = $this->manager()->getRepository("App:Categoria")->findBy(['negocio'=>$negocio],['descripcion'=>'ASC']);
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Post("/negocio/{negocio}/nuevo", name="nueva_categoria", defaults={"_format":"json"})
     * @SWG\Response(response=201,description="Producto creado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la categoria")
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="descripcion categoria",schema={})

     * @SWG\Tag(name="Categoria")
     */
    public function nuevaCategoria(Request $request, Negocio $negocio)
    {
        try {
            $errores = [];
            !empty($request->request->get('descripcion')) ? $descripcion = $request->request->get('descripcion')    :  $errores['descripcion'] = 'Este campo es obligatorio';

            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            $categoria = new Categoria($descripcion,$negocio);

            $this->manager()->persist($categoria);
            $this->manager()->flush();

            return $this->apiResponse($categoria,201);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/editar/{categoria}", name="editar_categoria", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza la catgegoria de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     *
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="descripcion de la categoria",schema={})
     * @SWG\Tag(name="Categoria")
     */
    public function editarCategoria(Request $request, Categoria $categoria )
    {
        try {
            $errores = [];
            !empty($request->request->get('descripcion')) ? $descripcion = $request->request->get('descripcion')    :  $errores['descripcion'] = 'Este campo es obligatorio';
            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            /** Actualizo los campos del producto */
            $categoria->setDescripcion($descripcion);

            $this->manager()->flush();

            return $this->apiResponse($categoria,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/eliminar/{categoria}", name="eliminar_categoria", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza la marca de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     *
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="descripcion de la marca",schema={})
     * @SWG\Tag(name="Categoria")
     */
    public function eliminarCategoria(Categoria $categoria)
    {
        try {
            $this->manager()->remove($categoria);
            $this->manager()->flush();

            return $this->apiResponse($categoria,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/eliminarCategorias", name="eliminar_categorias", defaults={"_format":"json"})
     * @Rest\RequestParam(name="categorias",nullable=false)
     * @SWG\Response(response=200,description="Elimina las categorias de  un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Categoria")
     */
    public function eliminarCategorias(ParamFetcher $paramFetcher)
    {
        try
        {
          $categorias = json_decode($paramFetcher->get('categorias'));
          /** begin transaccion */
          $this->manager()->getConnection()->beginTransaction();
          foreach ($categorias as $m)
          {
              $categoria = $this->manager()->getRepository("App:Categoria")->find($m->marca_id);
              $this->manager()->remove($categoria);
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
     * @Rest\Put("/incremento", name="incremento_categoria", defaults={"_format":"json"})
     * @Rest\RequestParam(name="categoria",nullable=true)
     * @Rest\RequestParam(name="incremento",nullable=true)
     * @Rest\RequestParam(name="cuentaCorriente",nullable=true)
     * @SWG\Response(response=200,description="Incrementa el porcentaje recibido a todos los productos por marca.")
     * @SWG\Response(response=500,description="Hubo un problema al incrementar los productos por marca")
     * @SWG\Tag(name="Producto")
     */
    public function incrementoPorCategoria(ParamFetcher $paramFetcher)
    {
        try
        {
            $estadoPendientePago = $this->getParameter('estado_pendiente_pago');
            $categoria = $this->manager()->getRepository("App:Categoria")->find($paramFetcher->get('categoria'));
            $incremento = $paramFetcher->get('incremento');
            $cuentaCorriente = !empty($paramFetcher->get('cuentaCorriente')) ? $paramFetcher->get('cuentaCorriente') : null;
            /** inicio transaccion */
            $this->manager()->getConnection()->beginTransaction();
            $productos = $this->manager()->getRepository("App:Producto")->findBy(['categoria'=>$categoria]);
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
              $productosPreventasCategorias = $this->manager()->getRepository("App:ProductoPreventa")->productosPreventaTipoProd($categoria,$estadoPendientePago,true);

              /** modifico el subtotal y precio unitario**/
              foreach ($productosPreventasCategorias as $value) {
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
      * @Rest\Get("/exportar/{negocio}", name="descargar_categorias", defaults={"_format":"json"})
      * @SWG\Response(response=200,description="Devuelve todas las categorias de un negocio.")
      * @SWG\Response(response=500,description="Hubo un problema para recuperar las categorias de un negocio")
      * @SWG\Tag(name="Cliente")
      */
     public function exportarCategorias(Negocio $negocio)
     {
         try
         {
             $categorias = $this->manager()->getRepository("App:Categoria")->findBy(array('negocio'=> $negocio), array('descripcion' => 'ASC'));
             $data = ['titulo'=>'CATEGORIAS','datos'=>$categorias];
             $pdfData = $this->generarPdf('pdf/marcasCategorias.html.twig',$data);
             $response =  array('file' => "data:application/pdf;base64,".$pdfData);
             return $this->apiResponse($response,200);
         } catch (Exception $e)
         {
             return $this->apiResponse($ex->getMessage(),500);
         }
     }


}
