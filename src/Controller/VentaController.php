<?php

namespace App\Controller;
use App\Entity\Venta;
use App\Entity\EstadoVenta;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\Extensions\PDFUtilitiesTrait;

/**
 * Class ApiController
 *
 * @Route("/api/ventas")
 */
class VentaController extends RestController
{

    use PDFUtilitiesTrait;
    /**
     * @Rest\Post("/pedido/nuevo", name="nueva_venta", defaults={"_format":"json"})
     * @Rest\RequestParam(name="cliente",nullable=false)
     * @Rest\RequestParam(name="productos",nullable=false)
     * @Rest\RequestParam(name="fecha",nullable=false)
     * @SWG\Response(response=201,description="Venta creada correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la marca")
     * @SWG\Tag(name="Venta")
     */
    public function nuevaPedido(ParamFetcher $paramFetcher)
    {
        try {
            $cliente = $this->manager()->getRepository("App:Cliente")->find($paramFetcher->get('cliente')['cliente_id']);
            $fecha = new \DateTime($paramFetcher->get('fecha'));

            /** verifico si en esa fecha ya se encuentra un pedido para ese cliente */
            $pedidoCliente = $this->manager()->getRepository("App:Venta")->findOneBy(['cliente'=>$cliente,
                                                                                       'fVenta'=>$fecha]);
            if(!empty($pedidoCliente)) {
              throw new Exception('Ya existe un pedido para el cliente en esa fecha');
            };
            /** Venta */
            $tipoVenta = $this->manager()->getRepository("App:TipoVenta")->findOneBy(['codigo'=>'PEDIDO']);//tipo pedido
            $venta = new Venta($cliente,$tipoVenta,$fecha);            
            $this->manager()->persist($venta);

            /** Estado Venta */
            $estado = $this->manager()->getRepository("App:Estado")->findOneBy(['codigo'=>'PENDIENTE']);
            $estadoVenta = new EstadoVenta($venta,$estado);
            $this->manager()->persist($estadoVenta);

            /** Productos Venta **/
            $productos = $paramFetcher->get('productos');
            $this->manager()->getRepository("App:ProductoVenta")->generarProductosVentas($productos,$venta);

            /** una vez creado el producto preventa se deberia notificar*/
            $this->manager()->flush();
            return $this->apiResponse($estadoVenta,201);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Get("/pedido/{venta}", name="pedido_data", defaults={"_format":"json"})
     * @SWG\Response(response=201,description="Obtiene la informacion de una venta ")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al obtener los pedidos")

     * @SWG\Tag(name="Venta")
     */
    public function pedidoInfo(Venta $venta)
    {
        try {
            /** una vez creado el producto preventa se deberia notificar*/
            return $this->apiResponse($venta,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Get("/pedidos/{codigo}", name="pedidos", defaults={"_format":"json"})
     * @SWG\Response(response=201,description="Obtiene los pedidos dependiendo el codigo de estado ")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al obtener los pedidos")

     * @SWG\Tag(name="Venta")
     */
    public function pedidos(string $codigo)
    {
        try {
            $negocio = $this->getUser()->getNegocio();
            $pedidos = $this->manager()->getRepository("App:Venta")->pedidos($negocio, $codigo);
            /** una vez creado el producto preventa se deberia notificar*/
            return $this->apiResponse($pedidos,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Get("/pedido/productos/{venta}", name="pedido_venta", defaults={"_format":"json"})
     * @SWG\Response(response=201,description="Obtiene los productos de un pedido")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al obtener los productos")

     * @SWG\Tag(name="Venta")
     */
    public function pedidoProductos(Venta $venta)
    {
        try {
            $productos = $this->manager()->getRepository("App:ProductoVenta")->findBy(['venta'=>$venta]);
            /** una vez creado el producto preventa se deberia notificar*/
            return $this->apiResponse($productos,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/pedido/estado/{venta}", name="pedido_cambio_estado", defaults={"_format":"json"})
     * @Rest\RequestParam(name="codigo",nullable=false)
     * @SWG\Response(response=200,description="Cambia el estado de un pedido")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al modificar el estado de un pedido")

     * @SWG\Tag(name="Venta")
     */
    public function cambiarEstado(ParamFetcher $paramFetcher, Venta $venta)
    {
        try {
            $estado = $this->manager()->getRepository("App:Estado")->findOneBy(['codigo'=>$paramFetcher->get('codigo')]);
            $estadoVenta = $this->manager()->getRepository("App:EstadoVenta")->findOneBy(['venta'=>$venta,'vigente'=>'S']);
            $estadoVenta->setVigente('N');

            $nuevoEstadoVenta = new EstadoVenta($venta,$estado);
            $this->manager()->persist($nuevoEstadoVenta);
            $this->manager()->flush();

            /** una vez creado el producto preventa se deberia notificar*/
            return $this->apiResponse($nuevoEstadoVenta,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/pedido/eliminar/{venta}", name="eliminar_pedido", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Cambia el estado de un pedido")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al modificar el estado de un pedido")

     * @SWG\Tag(name="Venta")
     */
    public function eliminarPedido(Venta $venta)
    {
        try {
            $venta->setFHasta(new \DateTime());
            $this->manager()->flush();
            /** una vez creado el producto preventa se deberia notificar*/
            return $this->apiResponse($venta,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Get("/pedido/descargar/{venta}", name="descargar_pedido", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Pedido Descargado")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al descargar el PDF")
     * @SWG\Tag(name="Venta")
     */
    public function descargarPedido(Venta $venta)
    {
        try
        {
            $productosPreventa = $this->manager()->getRepository("App:ProductoVenta")->findBy(['venta'=>$venta]);
            $data = ['venta'=>$venta,
                     'productos'=>$productosPreventa];
            $pdfBase64 = $this->obtenerPDF('pdf/pedido.html.twig',$data);
            $response =  array('file' => "data:application/pdf;base64,".$pdfBase64);
            return $this->apiResponse($response,200);
        } catch (Exception $e)
        {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Get("/estados", name="estados_ventas", defaults={"_format":"json"})
     * @SWG\Response(response=201,description="Obtiene los estados ")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al obtener los estados")
     * @SWG\Tag(name="Venta")
     */
    public function estados()
    {
        try {
            $estados = $this->manager()->getRepository("App:Estado")->findAll();
            /** una vez creado el producto preventa se deberia notificar*/
            return $this->apiResponse($estados,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }






}
