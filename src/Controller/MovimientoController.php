<?php

namespace App\Controller;

use App\Entity\EstadoVenta;
use App\Entity\Movimiento;
use App\Entity\Cliente;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Config\Definition\Exception\Exception;

use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * Class ApiController
 *
 * @Route("/api/movimientos")
 */
class MovimientoController extends RestController
{
     /**
     * @Rest\Post("/agregar", name="agregar_movimiento", defaults={"_format":"json"})
     * @Rest\RequestParam(name="cliente",nullable=false)
     * @Rest\RequestParam(name="valor",nullable=false)
     * @Rest\RequestParam(name="observacion",nullable=true)
     * @SWG\Response(response=200,description="Devuelve todas los clientes de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los clientes de un negocio")
     * @SWG\Tag(name="Movimiento")
     */
    public function agregarMovimiento(ParamFetcher $paramFetcher)
    {
        try{
            $this->manager()->getConnection()->beginTransaction();
            $monto = $paramFetcher->get('valor');
            $cliente = $this->manager()->getRepository('App:Cliente')->find($paramFetcher->get('cliente'));
            $observacion = $paramFetcher->get('observacion') ?? null;
            $ventasPendientePago = ($this->manager()->getRepository('App:Venta')->ventasPendientePago($cliente));
            foreach ($ventasPendientePago as $value) {
                $venta = $this->manager()->getRepository('App:Venta')->find($value['venta']['ventaId']);
                $montoDebido = $venta->getMontoDebido();
                /** verifico si el monto me alcanzan para cerrar la venta*/
                $montoDiferencial = $montoDebido - $monto;
                $venta->setMontoDebido($montoDiferencial < 0 ? 0 : $montoDiferencial);
                /** agrego el movimiento */
                $movimiento = new Movimiento($venta,$monto,$observacion);
                $this->manager()->persist($movimiento);
                /** me alcanzo para cerrar la venta */
                if($montoDiferencial <= 0){
                    /** cambio la vigencia del estaado venta actual por N */
                    $estadoVentaAnterior = $this->manager()->getRepository('App:EstadoVenta')->findOneBy(['venta'=>$venta,'vigente'=>'S']);
                    $estadoVentaAnterior->setVigente('N');
                    /** creo un nuevo estado venta vigente */
                    $estadoPendienteComprobante = $this->manager()->getRepository('App:Estado')->findOneBy(['codigo'=>'PENDIENTECOMPROBANTE']);
                    $estadoVentaNuevo = new EstadoVenta($venta,$estadoPendienteComprobante);
                    $this->manager()->persist($estadoVentaNuevo);
                }
                else{
                    break;
                }
            }
            $this->manager()->flush();
            $this->manager()->getConnection()->commit();
            return $this->apiResponse([],200);
        } catch (Exception $e) {
            $this->manager()->getConnection()->rollback();
            return $this->apiResponse($e->getMessage(),500);
        }
    }


     /**
     * @Rest\Get("/cliente/{cliente}", name="movimientos_clientes", defaults={"_format":"json"})
     * @Rest\QueryParam(name="limit",nullable=false)
     * @SWG\Response(response=200,description="Devuelve todas los clientes de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los clientes de un negocio")
     * @SWG\Tag(name="Movimiento")
     */
    public function movimientos(ParamFetcher $paramFetcher, Cliente $cliente)
    {
        try{
            $limit = !empty($paramFetcher->get('limit')) ? $paramFetcher->get('limit') : null;
            $movimientos = $this->manager()->getRepository("App:Cliente")->movimientos($cliente,$limit);
            return $this->apiResponse($movimientos,200);
        } catch (Exception $e) {
            $this->manager()->getConnection()->rollback();
            return $this->apiResponse($e->getMessage(),500);
        }
    }


}
