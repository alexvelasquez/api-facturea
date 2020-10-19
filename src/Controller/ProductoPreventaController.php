<?php

namespace App\Controller;

use App\Entity\Negocio;
use App\Entity\TipoPreventa;
use App\Entity\Preventa;
use App\Entity\ProductoPreventa;
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
 * @Route("/api/productoPreventa")
 */
class ProductoPreventaController extends RestController
{

    /**
     * @Rest\Delete("/eliminar/{productoPreventa}", name="eliminar_producto_preventa", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Eliminado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la marca")
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="nombre del producto",schema={})

     * @SWG\Tag(name="Preventa")
     */
    public function eliminarProductoPreventa(ProductoPreventa $productoPreventa)
    {
        try {
            /** actualizo el stock */
            $producto = $productoPreventa->getProducto();
            $producto->setStock($producto->getStock() + $productoPreventa->getCantidad());
            /** elimino el producto preventa */
            $this->manager()->remove($productoPreventa);
            $this->manager()->flush();
            return $this->apiResponse($productoPreventa,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }


}
