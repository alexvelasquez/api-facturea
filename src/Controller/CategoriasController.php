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
 * @Route("/api/categorias")
 */
class CategoriasController extends RestController
{


     /**
     * @Rest\Get("/negocio/{negocio}", name="lista_categorias", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas las categorias")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar las categorias")
     * @SWG\Tag(name="Categoria")
     */
    public function categoriasNegocio(Negocio $negocio)
    {
        try{
            $response = $this->manager()->getRepository("App:Categoria")->findBy(['negocio'=>$negocio],['descripcion'=>'ASC']);
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Post("/negocio/{negocio}/nuevo", name="nueva_categoria", defaults={"_format":"json"})
     * @SWG\Response(response=201,description="Producto creado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear la categoria")
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="descripcion categoria",schema={})

     * @SWG\Tag(name="Categoria")
     */
    public function nuevaCategoria(Request $request, Negocio $negocio)
    {
        try {
            $errores = [];
            !empty($request->request->get('descripcion')) ? $descripcion = $request->request->get('descripcion')    :  $errores['descripcion'] = 'Este campo es obligatorio';

            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            $categoria = new Categoria($descripcion,$negocio);

            $this->manager()->persist($categoria);
            $this->manager()->flush();

            return $this->apiResponse($categoria,201);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/editar/{categoria}", name="editar_categoria", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza la catgegoria de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     *
     * @SWG\Parameter(name="descripcion",in="body",type="string",description="descripcion de la categoria",schema={})
     * @SWG\Tag(name="Categoria")
     */
    public function editarCategoria(Request $request, Categoria $categoria )
    {
        try {
            $errores = [];
            !empty($request->request->get('descripcion')) ? $descripcion = $request->request->get('descripcion')    :  $errores['descripcion'] = 'Este campo es obligatorio';
            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            /** Actualizo los campos del producto */
            $categoria->setDescripcion($descripcion);

            $this->manager()->flush();

            return $this->apiResponse($categoria,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

}
