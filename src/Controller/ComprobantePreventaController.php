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
     * @Rest\QueryParam(name="fechaDesde",nullable=false)
     * @Rest\QueryParam(name="fechaHasta",nullable=false)
     * @SWG\Response(response=200,description="Ventas correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la marca")
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="nombre del producto",schema={})

     * @SWG\Tag(name="Comprobante Preventa")
     */
    public function ventasComprobantePreventa(ParamFetcher $paramFetcher, Negocio $negocio)
    {
        try {
            $pendiente =$this->getParameter('estado_pendiente');
            $pagado =$this->getParameter('estado_pagado');
            $pendientePago =$this->getParameter('estado_pendiente_pago');
            $realizado =$this->getParameter('estado_realizado');
            $tipoPreventa = $this->getParameter('tipo_preventa_pedido');
            $tipoRecibo =$this->getParameter('tipo_comprobante_recibo');

            $periodo = ['fechaDesde' => $paramFetcher->get('fechaDesde').' 00:00:00',
                        'fechaHasta' => $paramFetcher->get('fechaHasta').' 23:59:59'];
            $estados= [ (string) $pendiente => 'pendiente',
                        (string) $pagado => 'pagado',
                        (string) $pendientePago => 'pendientePago',
                        (string) $realizado => 'realizado'
                      ];
            $recaudacionesTotales = $this->manager()->getRepository("App:ComprobantePreventa")->recaudacionTotal($negocio,$pagado,$pendientePago,$periodo);
            $comprobantesTotales = $this->manager()->getRepository("App:ComprobantePreventa")->comprobantesTotales($negocio,$pagado,$periodo);
            $pedidosTotales = $this->manager()->getRepository("App:ComprobantePreventa")->pedidosTotales($negocio,$pagado,$realizado,$pendiente,$tipoPreventa,$periodo);
            //dd($pedidosTotales);
             //dd($comprobantesTotales);
            //$comprobantes totales
            $data['totales']['recaudacion']=[];
            $data['totales']['comprobantes']=[];
            $data['totales']['pedidos']=[];
            //foreach totales
            foreach ($recaudacionesTotales as $value) {
                $indiceEstado = $estados[$value['estado'] ];//string del estado;
                $indiceFecha = $value['fecha'];
                $total = $value['total'];
                $montosEstados = $data['totales']['recaudacion'][$indiceEstado] ?? 0;
                $data['totales']['recaudacion'][$indiceEstado] = round($montosEstados + $total,2);

                $montoTotal = $data['totales']['recaudacion']['total'] ?? 0;
                $data['totales']['recaudacion']['total'] = round( $montoTotal + $total,2);

                if($value['estado'] == $pagado){
                  $monto = $data['graficos'][$indiceFecha] ?? 0;
                  $data['graficos'][$indiceFecha] = round($monto + $total,2);
                }
            }
            /** foreach de comprobantes **/
            foreach ($comprobantesTotales as $value) {
                $indiceComprobante = ($value['tipoComprobante'] == $tipoRecibo) ? 'recibo' : 'factura';//string del estado;

                $cantidad = $value['cantidad'];
                $cantidadComprobantes = $data['totales']['comprobantes'][$indiceComprobante] ?? 0;
                $data['totales']['comprobantes'][$indiceComprobante] = $cantidadComprobantes + $cantidad;

                $cantidadTotal = $data['totales']['comprobantes']['total'] ?? 0;
                $data['totales']['comprobantes']['total'] = $cantidadTotal + $cantidad;

            }

            /** foreach de pedidos **/
            foreach ($pedidosTotales as $value) {
                $indicePedido = $estados[$value['estado']] == 'pagado' ? 'realizado' : $estados[$value['estado']];//string del estado;
                $cantidad = $value['cantidad'];

                $cantidadComprobantes = $data['totales']['pedidos'][$indicePedido] ?? 0;
                $data['totales']['pedidos'][$indicePedido] = $cantidadComprobantes + $cantidad;

                $cantidadTotal = $data['totales']['pedidos']['total'] ?? 0;
                $data['totales']['pedidos']['total'] = $cantidadTotal + $cantidad;
            }

            /** formato para datos del grafico */
            if(!empty($data['graficos'])){
              $datasets = array_values($data['graficos']);
              $labels = array_keys($data['graficos']);
              $data['graficos']=[];/** borro los datos anteriores */
              $data['graficos']['datasets'] = $datasets;
              $data['graficos']['labels'] = $labels;
            }
            return $this->apiResponse($data,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }


}
