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
     * @Rest\RequestParam(name="observacion",nullable=false)
     * @SWG\Response(response=200,description="Devuelve todas los clientes de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los clientes de un negocio")
     * @SWG\Tag(name="Movimiento")
     */
    public function agregarMovimiento(ParamFetcher $paramFetcher)
    {
        try{
            $cuentaCorriente = $paramFetcher->get('cuenta_corriente');
            $valor = $paramFetcher->get('valor');
            $observacion = $paramFetcher->get('observacion');

            $movimiento = new Movimiento($cuentaCorriente,$valor,$observacion);
            $this->manager()->persist($movimiento);
            $this->manager()->flush();
            return $this->apiResponse($movimiento,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

}
