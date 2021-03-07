<?php

namespace App\Controller;

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
            $valor = $paramFetcher->get('valor');
            $observacion = $paramFetcher->get('observacion') ?? null;
            $cuentaCorriente = $this->manager()->getRepository('App:CuentaCorriente')->find($paramFetcher->get('cuenta_corriente'));
            $tipoMovimiento = $this->manager()->getRepository('App:TipoMovimiento')->findOneBy(['codigo'=>'DECREMENTO']);
            $movimiento = new Movimiento($cuentaCorriente,$valor,$tipoMovimiento,$observacion);
            $this->manager()->persist($movimiento);
            $cuentaCorriente = $cuentaCorriente->abonar($valor,$movimiento);
            
            // $estadoPendiente = $this->manager()->getRepository('App:Estado')->findOneBy(['codigo'=>'PENDIENTE']);
            // $ventasPendientes = $this->manager()->getRepository('App:EstadoVenta')->findBy(['estado'=>$estadoPendiente],['fCreacion'=>'ASC']);
            // $movimientosPendientes = $this->manager()->getRepository('App:Movimiento')->findBy()
            // /** proceso las ventas pendientes */
            // foreach ($ventas as $key => $value) {
            //     # code...
            // }

            $this->manager()->flush();
            return $this->apiResponse($cuentaCorriente,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

}
