<?php

namespace App\Controller;

;
use App\Entity\Negocio;
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
 * @Route("/api/notificaciones")
 */
class NotificacionesController extends RestController
{


     /**
     * @Rest\Get("", name="notificaciones", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas las categorias")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar las categorias")
     * @SWG\Tag(name="Categoria")
     */
    public function notificacionesUser()
    {
        try{
            $response = $this->manager()->getRepository("App:Notificacion")->findOneBy(['usuario'=>$this->getUser(),'leido'=>'N']);
            if(!empty($response)){
              $response->setLeido('S');
              $this->manager()->flush();
            }
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }



}
