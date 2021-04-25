<?php

namespace App\Controller;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Request\ParamFetcher;
use Swagger\Annotations as SWG;
use App\Entity\Notificacion;

/**
 * Class ApiController
 *
 * @Route("/api/notificaciones")
 */
class NotificacionesController extends RestController
{


     /**
     * @Rest\Get("", name="notificaciones", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve la ultima notificacion no leida del usuario")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar las notificacion")
     * @SWG\Tag(name="Notificacion")
     */
    public function notificacionesUser()
    {
        try{
            $response = $this->manager()->getRepository("App:Notificacion")->findOneBy(['usuario'=>$this->getUser(),'leido'=>'N']);
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
    
        }    
    }

    /**
     * @Rest\Post("nueva", name="crear_notificacion", defaults={"_format":"json"})
     * @Rest\RequestParam(name="titulo",nullable=false)
     * @Rest\RequestParam(name="mensaje",nullable=false)
     * @Rest\RequestParam(name="user",nullable=false)
     * @Rest\RequestParam(name="redireccion",nullable=false)
     * @SWG\Response(response=200,description="Crea una notificacion para un usuario")
     * @SWG\Response(response=500,description="Hubo un problema")
     * @SWG\Tag(name="Notificacion")
     */
    public function crearNotificacion(ParamFetcher $paramFetcher)
    {
        try{
            $titulo = $paramFetcher->get('titulo');
            $mensaje = $paramFetcher->get('mensaje');
            $user = $this->manager()->getRepository("App:User")->find($paramFetcher->get('user'));
            $redireccion = $paramFetcher->get('redirección');

            $notificacion = new Notificacion($titulo,$mensaje,$user,$redireccion);
            $this->manager()->persist($notificacion);
            $this->manager()->flush();
            return $this->apiResponse($notificacion,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
    
        }    
    }
     /**
     * @Rest\Post("/confirmar/{notificacion}", name="confirmar", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Confirma una notificacion")
     * @SWG\Response(response=500,description="Hubo un problema al confirmar la notificacion")
     * @SWG\Tag(name="Notificacion")
     */
    public function confirmarNotificacion(Notificacion $notificacion)
    {
        try{
            $notificacion->setLeido('S');
            $this->manager()->flush();
            return $this->apiResponse($notificacion,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

}
