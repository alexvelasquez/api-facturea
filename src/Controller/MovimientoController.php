<?php

namespace App\Controller;

use App\Entity\EstadoVenta;
use App\Entity\Movimiento;
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
     * @Rest\RequestParam(name="cuenta_corriente",nullable=false)
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
            $valor = $paramFetcher->get('valor');
            $observacion = $paramFetcher->get('observacion') ?? null;
            $cuentaCorriente = $this->manager()->getRepository('App:CuentaCorriente')->find($paramFetcher->get('cuenta_corriente'));
            $tipoMovimiento = $this->manager()->getRepository('App:TipoMovimiento')->findOneBy(['codigo'=>'DECREMENTO']);
            $movimiento = new Movimiento($cuentaCorriente,$valor,$tipoMovimiento,$observacion);
            $this->manager()->persist($movimiento);

            $cuentaCorriente = $cuentaCorriente->abonar($valor,$movimiento);
            
            /** sumo el nuevo movimiento */
            $montoAFavor = ($cuentaCorriente->getMontoFavor()+$movimiento->getValor());

            $ventasPendientePago = ($this->manager()->getRepository('App:Venta')->ventasPendientePago($cuentaCorriente->getCliente()));
            foreach ($ventasPendientePago as $value) {
                $estadoVenta = $value['estado_venta'];
                $montoVenta = $value['total'];
                /** verifico si el monto me alcanzan para cerrar la venta*/
                $diferenciaPago = $montoVenta - $montoAFavor;
                /** me alcanzo para cerrar la venta */
                if($diferenciaPago <= 0){
                    /** cambio la vigencia del estaado venta actual */
                    $venta = $this->manager()->getRepository('App:Venta')->find($estadoVenta['venta']['ventaId']);
                    $estadoVentaAnterior = $this->manager()->getRepository('App:EstadoVenta')->find($estadoVenta['estadoVentaId']);
                    $estadoVentaAnterior->setVigente('N');
                    /** creo un nuevo estado venta vigente */
                    $estadoPendienteComprobante = $this->manager()->getRepository('App:Estado')->findOneBy(['codigo'=>'PENDIENTECOMPROBANTE']);
                    $estadoVentaNuevo = new EstadoVenta($venta,$estadoPendienteComprobante);
                    $this->manager()->persist($estadoVentaNuevo);
                    /** cambio el el valor negativo a positivo */
                    $montoAFavor = ($diferenciaPago <= 0) ? ($diferenciaPago * (-1)) : $diferenciaPago;
                }
                else{
                    break;
                }
            }
            $cuentaCorriente->setMontoFavor($montoAFavor); 
            $cuentaCorriente->setFModificacion(new \DateTime());
            $this->manager()->flush();
            $this->manager()->getConnection()->commit();
            return $this->apiResponse($cuentaCorriente,200);
        } catch (Exception $e) {
            $this->manager()->getConnection()->rollback();
            return $this->apiResponse($e->getMessage(),500);
        }
    }

}
