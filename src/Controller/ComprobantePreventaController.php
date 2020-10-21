<?php

namespace App\Controller;

use App\Entity\Negocio;
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

/**
 * Class ApiController
 *
 * @Route("/api/comprobantePreventa")
 */
class ComprobantePreventaController extends RestController
{

    /**
     * @Rest\Get("/negocio/{negocio}/ventas", name="ventas_totales", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Ventas correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la marca")
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="nombre del producto",schema={})

     * @SWG\Tag(name="Comprobante Preventa")
     */
    public function ventasComprobantePreventa(Negocio $negocio)
    {
        try {
            /** actualizo el stock */
            $estados=['pagado'=>$this->getParameter('estado_pagado'),
                      'pendientePago'=>$this->getParameter('estado_pendientePago')];
            $totales = $this->manager()->getRepository("App:ComprobantePreventa")->ventasTotales($negocio,$estados);
            $response['total']=0;

            $response[$this->getParameter('estado_pagado')]=0;
            $response[$this->getParameter('estado_pendientePago')]=0;

            $valoresPredefinidos = array_fill(0, (int) date('n'), 0);
            $graficos[$this->getParameter('estado_pagado')]=$valoresPredefinidos;
            $graficos[$this->getParameter('estado_pendientePago')]=$valoresPredefinidos;
            $graficos['total']=$valoresPredefinidos;

            foreach ($totales as $value) {
                $mes = (int) $value['mes'];
                $total = $value['total'];
                $estado = $value['estado'];
                /** datos para el grafico */
                $graficos[$estado][$mes-1] = round($total,2);
                $graficos['total'][$mes-1] = round($graficos['total'][$mes-1]+$total,2);
                /** totales */
                $response[$estado] = round($response[$estado]+$total,2);
                $response['total'] = round($response['total']+$total,2);
            }

            $data['graficos']=$graficos;
            $data['totales']=$response;
            return $this->apiResponse($data,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }


}
