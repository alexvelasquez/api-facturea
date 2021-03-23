<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Config\Definition\Exception\Exception;

use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

/**
 * Class ApiController
 *
 * @Route("/api/inicio")
 */
class HomeController extends RestController
{
     /**
     * @Rest\Get("", name="recaudacion", defaults={"_format":"json"})
     * @Rest\QueryParam(name="fechaDesde",nullable=false)
     * @Rest\QueryParam(name="fechaHasta",nullable=false)
     * @SWG\Response(response=200,description="Devuelve todas los clientes de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los clientes de un negocio")
     * @SWG\Tag(name="Home")
     */
    public function valoresIniciales(ParamFetcher $paramFetcher)
    {
        try{
            $negocio = $this->getUser()->getNegocio();
            $fechaDesde = new \DateTime($paramFetcher->get('fechaDesde'));
            $fechaHasta = (new \DateTime($paramFetcher->get('fechaHasta')))->modify('+23 hours');
            /** recaudacion por fechas */
            $recaudacion = $this->manager()->getRepository('App:Venta')->recaudacionReporte($negocio,$fechaDesde,$fechaHasta);
            $comprobantes = $this->manager()->getRepository('App:Comprobante')->comprobantesReporte($negocio,$fechaDesde,$fechaHasta);
            $resultados = array_merge($recaudacion,$comprobantes);
            if($negocio->getPedido() === 'S'){
                $pedidos = $this->manager()->getRepository('App:Venta')->pedidosReporte($negocio,$fechaDesde,$fechaHasta);
                $resultados = array_merge($resultados,$pedidos);
            }
            $dataGraficos = $this->manager()->getRepository('App:Venta')->comprasGraficos($negocio,$fechaDesde,$fechaHasta);
            if(!empty($dataGraficos)){
                $graficos = [];
                foreach ($dataGraficos as $value) {
                    $monto =  array_key_exists($value['fecha'],$graficos) ? $graficos[$value['fecha']] : 0;
                    $graficos[$value['fecha']] = $monto + $value['total'];
                }
                $dataGraficos=['labels'=>array_keys($graficos),'values'=>array_values($graficos)];
                $dataGraficos=['graficos'=>$dataGraficos];
                $resultados = array_merge($resultados,$dataGraficos);
            }
            return $this->apiResponse($resultados,200);
        } catch (Exception $e) {
            // $this->manager()->getConnection()->rollback();
            return $this->apiResponse($e->getMessage(),500);
        }
    }


}
