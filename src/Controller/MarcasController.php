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
 * @Route("/api/marcas")
 */
class MarcasController extends RestController
{


     /**
     * @Rest\Get("/negocio/{negocio}", name="lista_marca", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
     * @SWG\Tag(name="Marca")
     */
    public function marcasNegocio( Negocio $negocio)
    {
        try
        {
            $response = $this->manager()->getRepository("App:Marca")->findBy(array('negocio'=> $negocio), array('descripcion' => 'ASC'));
            return $this->apiResponse($response,200);
        } catch (Exception $e)
        {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Post("/negocio/{negocio}/nuevo", name="nueva_marca", defaults={"_format":"json"})
     * @SWG\Response(response=201,description="Producto creado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la marca")
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="nombre del producto",schema={})

     * @SWG\Tag(name="Marca")
     */
    public function nuevaMarca(Request $request, Negocio $negocio)
    {
        try {
            $errores = [];
            !empty($request->request->get('descripcion')) ? $descripcion = $request->request->get('descripcion')    :  $errores['descripcion'] = 'Este campo es obligatorio';

            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            $marca = new Marca($descripcion,$negocio);
            $this->manager()->persist($marca);
            $this->manager()->flush();

            return $this->apiResponse($marca,201);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/editar/{marca}", name="editar_marca", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza la marca de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     *
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="descripcion de la marca",schema={})
     * @SWG\Tag(name="Marca")
     */
    public function editarMarca(Request $request, Marca $marca )
    {
        try {
            $errores = [];
            !empty($request->request->get('descripcion')) ? $descripcion = $request->request->get('descripcion')    :  $errores['descripcion'] = 'Este campo es obligatorio';
            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            /** Actualizo los campos del producto */
            $marca->setDescripcion($descripcion);

            $this->manager()->flush();

            return $this->apiResponse($marca,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }


}
