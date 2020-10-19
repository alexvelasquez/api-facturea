<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Negocio;
use App\Entity\Marca;
use App\Entity\Categoria;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Class ApiController
 *
 * @Route("/api/productos")
 */
class ProductosController extends RestController
{


     /**
     * @Rest\Get("/negocio/{negocio}", name="lista_productos", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas los productos de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los productos de un negocio")
     * @SWG\Tag(name="Producto")
     */
    public function productosNegocio( Negocio $negocio)
    {
        try{
            $response = $this->manager()->getRepository("App:Producto")->findBy(['negocio'=>$negocio,'fHasta'=>NULL]);
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Post("/negocio/{negocio}/nuevo", name="nuevo_producto", defaults={"_format":"json"})
     * @SWG\Response(response=201,description="Producto creado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear el producto")
     * @SWG\Parameter(name="nombre",in="body",type="string",description="nombre del producto",schema={})
     * @SWG\Parameter(name="stock",in="body",type="number",description="stock del producto",schema={})
     * @SWG\Parameter(name="marca",in="body",type="number",description="marca del producto",schema={})
     * @SWG\Parameter(name="categoria",in="body",type="number",description="categoria del producto",schema={})
     * @SWG\Parameter(name="precio",in="body",type="number",description="precio del producto",schema={})
     * @SWG\Parameter(name="incremento",in="body",type="string",description="incremento (porcentaje)",schema={})
     * @SWG\Tag(name="Producto")
     */
    public function nuevoProducto(Request $request, Negocio $negocio)
    {
        try {
            $errores = [];
            !empty($request->request->get('descripcion')) ? $descripcion = $request->request->get('descripcion')    :  $errores['descripcion'] = 'Este campo es obligatorio';
            !empty($request->request->get('codigo')) || $request->request->get('codigo') != "0"  ? $codigo  = $request->request->get('codigo')     :  $errores['codigo'] = 'Este campo es obligatorio';
            !empty($request->request->get('stock'))  ? $stock  = $request->request->get('stock')     :  $errores['stock'] = 'Este campo es obligatorio';
            !empty($request->request->get('categoria'))  ? $categoria  = $request->request->get('categoria')['categoria_id']    :  $errores['categoria'] = 'Este campo es obligatorio';
            !empty($request->request->get('marca'))  ? $marca  = $request->request->get('marca')['marca_id']    :  $errores['marca'] = 'Este campo es obligatorio';
            !empty($request->request->get('precio_compra')) ? $precioCompra = $request->request->get('precio_compra')    :  $errores['precio_compra'] = 'Este campo es obligatorio' ;
            !empty($request->request->get('aumento')) ? $aumento = $request->request->get('aumento')    :  $errores['aumento'] = 'Este campo es obligatorio' ;
            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            $categoria = $this->manager()->getRepository("App:Categoria")->find($categoria);
            $marca = $this->manager()->getRepository("App:Marca")->find($marca);
            $producto = new Producto($descripcion,$codigo,$stock,$categoria,$marca,$precioCompra,$aumento,$negocio);

            $this->manager()->persist($producto);
            $this->manager()->flush();

            return $this->apiResponse($producto,201);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/editar/{producto}", name="editar_producto", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza el producto de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     *
     * @SWG\Parameter(name="nombre",in="body",type="string",description="nombre del producto",schema={})
     * @SWG\Parameter(name="codigo",in="body",type="string",description="codigo del producto",schema={})
     * @SWG\Parameter(name="stock",in="body",type="number",description="sotck del producto",schema={})
     * @SWG\Parameter(name="marca",in="body",type="number",description="marca del producto",schema={})
     * @SWG\Parameter(name="categoria",in="body",type="number",description="categoria del producto",schema={})
     * @SWG\Parameter(name="precio",in="body",type="number",description="precio del producto",schema={})
     * @SWG\Parameter(name="incremento",in="body",type="string",description="incremento (porcentaje)",schema={})
     * @SWG\Tag(name="Producto")
     */
    public function editarProducto(Request $request, Producto $producto )
    {

        try {
            $errores = [];
            !empty($request->request->get('descripcion')) ? $descripcion = $request->request->get('descripcion')    :  $errores['descripcion'] = 'Este campo es obligatorio';
            !empty($request->request->get('codigo'))  ? $codigo  = $request->request->get('codigo')     :  $errores['codigo'] = 'Este campo es obligatorio';
            !empty($request->request->get('stock'))  ? $stock  = $request->request->get('stock')     :  $errores['stock'] = 'Este campo es obligatorio';
            !empty($request->request->get('categoria'))  ? $categoria  = $request->request->get('categoria')['categoria_id']    :  $errores['categoria'] = 'Este campo es obligatorio';
            !empty($request->request->get('marca'))  ? $marca  = $request->request->get('marca')['marca_id']   :  $errores['marca'] = 'Este campo es obligatorio';
            !empty($request->request->get('precio_compra')) ? $precioCompra = $request->request->get('precio_compra')    :  $errores['precio_compra'] = 'Este campo es obligatorio' ;
            !empty($request->request->get('aumento')) ? $aumento = $request->request->get('aumento')    :  $errores['aumento'] = 'Este campo es obligatorio' ;
            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            $categoria = $this->manager()->getRepository("App:Categoria")->find($categoria);
            $marca = $this->manager()->getRepository("App:Marca")->find($marca);
            /** Actualizo los campos del producto */
            $producto->setDescripcion($descripcion);
            $producto->setCodigo($codigo);
            $producto->setStock($stock);
            $producto->setCategoria($categoria);
            $producto->setMarca($marca);
            $producto->setPrecioCompra($precioCompra);
            $producto->setAumento($aumento);
            $producto->setFModificacion(new \DateTime());

            $this->manager()->flush();

            return $this->apiResponse($producto,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/eliminar/{producto}", name="eliminar_producto", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza el producto de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Producto")
     */
    public function eliminarProducto(Producto $producto )
    {

        try {
            /** Actualizo los campos del producto */
            $producto->setFModificacion(new \DateTime());
            $producto->setFHasta(new \DateTime());
            $this->manager()->flush();

            return $this->apiResponse($producto,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/eliminarProductos", name="eliminar_producto", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza el producto de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Producto")
     */
    public function eliminarProductos(Request $request)
    {
        $errores = [];

        !empty($request->request->get('productos')) ? $productos = json_decode($request->request->get('productos')) : $errores['productos'] = 'Este campo es obligatorio' ;
        if(!empty($errores))
        {
          return $this->apiResponse($errores,400);
        }
        try
        {
          /** begin transaccion */
          $this->manager()->getConnection()->beginTransaction();
          foreach ($productos as $p)
          {
              $producto = $this->manager()->getRepository("App:Producto")->find($p->producto_id);
              /** Actualizo los campos del producto */
              $producto->setFModificacion(new \DateTime());
              $producto->setFHasta(new \DateTime());
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
     * @Rest\Put("/incremento/{negocio}", name="incremento_productos", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Incrementa el porcentaje recibido a todos los productos de su negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los productos de un negocio")
     * @SWG\Tag(name="Producto")
     */
    public function incrementoTotal(Request $request, Negocio $negocio)
    {
        try
        {
            $errores = [];
            !empty($request->request->get('incremento')) ? $incremento = $request->request->get('incremento') : $errores['incremento'] = 'Este campo es obligatorio' ;
            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            /** inicio transaccion */
            $this->manager()->getConnection()->beginTransaction();
            $productos = $this->manager()->getRepository("App:Producto")->findBy(['negocio'=>$negocio]);
            foreach ($productos as $producto)
            {
                $producto->setIncremento($incremento);
                $producto->setPrecioPublicado();
                $producto->setFModificacion(new \DateTime());
            }
            $this->manager()->flush();
            /** commit transaccion */
            $this->manager()->getConnection()->commit();
            return $this->apiResponse($productos,200);

        } catch (Exception $e)
        {
            /** rollback transaccion */
            $this->manager()->getConnection()->rollback();
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/incremento/marca/{marca}", name="incremento_marca_productos", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Incrementa el porcentaje recibido a todos los productos por marca.")
     * @SWG\Response(response=500,description="Hubo un problema al incrementar los productos por marca")
     * @SWG\Tag(name="Producto")
     */
    public function incrementoPorMarca(Request $request, Marca $marca)
    {
        try
        {
            $errores = [];
            !empty($request->request->get('incremento')) ? $incremento = $request->request->get('incremento') : $errores['incremento'] = 'Este campo es obligatorio' ;
            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            /** inicio transaccion */
            $this->manager()->getConnection()->beginTransaction();
            $productos = $this->manager()->getRepository("App:Producto")->findBy(['marca'=>$marca]);
            foreach ($productos as $producto)
            {
                $producto->setIncremento($incremento);
                $producto->setPrecioPublicado();
                $producto->setFModificacion(new \DateTime());
            }
            $this->manager()->flush();
            /** commit transaccion */
            $this->manager()->getConnection()->commit();
            return $this->apiResponse($productos,200);
        }catch (Exception $e)
        {
            /** rollback transaccion */
            $this->manager()->getConnection()->rollback();
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

        /**
     * @Rest\Put("/incremento/categoria/{categoria}", name="incremento_categoria_productos", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Incrementa el porcentaje recibido a todos los productos por categoria.")
     * @SWG\Response(response=500,description="Hubo un problema al incrementar los productos por categoria")
     * @SWG\Tag(name="Producto")
     */
    public function incrementoPorCategoria(Request $request, Categoria $categoria)
    {
        try
        {
            $errores = [];
            !empty($request->request->get('incremento')) ? $incremento = $request->request->get('incremento') : $errores['incremento'] = 'Este campo es obligatorio' ;
            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            /** inicio transaccion */
            $this->manager()->getConnection()->beginTransaction();
            $productos = $this->manager()->getRepository("App:Producto")->findBy(['categoria'=>$categoria]);
            foreach ($productos as $producto)
            {
                $producto->setIncremento($incremento);
                $producto->setPrecioPublicado();
                $producto->setFModificacion(new \DateTime());
            }
            $this->manager()->flush();
            /** commit transaccion */
            $this->manager()->getConnection()->commit();
            return $this->apiResponse($productos,200);
        }catch (Exception $e)
        {
            /** rollback transaccion */
            $this->manager()->getConnection()->rollback();
            return $this->apiResponse($ex->getMessage(),500);
        }
    }
}
