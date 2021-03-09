<?php

namespace App\Controller;

use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation\Route;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Swagger\Annotations as SWG;

/**
 * Class ApiController
 *
 * @Route("/api/usuario")
 */
class UsuarioController extends RestController
{

     /**
     * @Rest\Get("", name="lista_usuarios", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todos los usuarios de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los usuarios de un negocio")
     * @SWG\Tag(name="Usuario")
     */
    public function usuarios()
    {
        try{
            $response = $this->manager()->getRepository("App:User")->findRoleUser();
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }


    /**
     * @Rest\Put("/editar", name="editar_usuario", defaults={"_format":"json"})
     * @Rest\RequestParam(name="apellido",nullable=false)
     * @Rest\RequestParam(name="email",nullable=false)
     * @Rest\RequestParam(name="nombre",nullable=false)
     * @Rest\RequestParam(name="nombreUsuario",nullable=false)
     * @SWG\Response(response=200,description="Devuelve todos los usuarios de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los usuarios de un negocio")
     * @SWG\Tag(name="Usuario")
     */
    public function editarUsuario(ParamFetcher $paramFetcher)
    {
        try{
            $name = $paramFetcher->get('nombre');
            $username  = $paramFetcher->get('nombreUsuario');
            $email = $paramFetcher->get('email');
            $lastname = $paramFetcher->get('apellido');

            $user = $this->getUser();
            $user->setName($name);
            $user->setLastName($lastname);
            $user->setEmail($email);
            $user->setUsername($username);

            $this->manager()->flush();
            return $this->apiResponse($user,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/editarClave", name="editar_clave_usuario", defaults={"_format":"json"})
     * @Rest\RequestParam(name="actual",nullable=false)
     * @Rest\RequestParam(name="nueva",nullable=false)
     * @SWG\Response(response=200,description="Devuelve el usuario con la contraseña actualizada.")
     * @SWG\Response(response=500,description="Hubo un problema al actualizar la contraseña")
     * @SWG\Tag(name="Usuario")
     */
    public function editarClave(ParamFetcher $paramFetcher,UserPasswordEncoderInterface $encoder)
    {
        try{
            $actual = $paramFetcher->get('actual');
            $nueva  = $paramFetcher->get('nueva');

            $user = $this->getUser();
            $passwordActual = $encoder->encodePassword($user,$actual);
            if($user->getPassword() !== $passwordActual) return $this->apiResponse('Contraseña incorrecta',400);
            $passwordNueva = $encoder->encodePassword($user,$nueva);
            $user->setPassword($passwordNueva);
            $this->manager()->flush();
            return $this->apiResponse($user,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }


    /**
     * @Rest\Put("/gestionFacturaElectronica/{usuario}", name="activar_factura", defaults={"_format":"json"})
     * @Rest\RequestParam(name="valor",nullable=false)
     * @SWG\Response(response=200,description="Devuelve el usuario con la contraseña actualizada.")
     * @SWG\Response(response=500,description="Hubo un problema al actualizar la contraseña")
     * @SWG\Tag(name="Usuario")
     */
    public function activarFactura(ParamFetcher $paramFetcher, User $usuario)
    {
        try{
            $valor = $paramFetcher->get('valor');
            $usuario->getNegocio()->setFacturaElectronica($valor);
            $this->manager()->flush();
            return $this->apiResponse($this->getUser(),200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }
    /**
     * @Rest\Put("/gestionPedido/{usuario}", name="activar_pedido", defaults={"_format":"json"})
     * @Rest\RequestParam(name="valor",nullable=false)
     * @SWG\Response(response=200,description="Devuelve el usuario con la contraseña actualizada.")
     * @SWG\Response(response=500,description="Hubo un problema al actualizar la contraseña")
     * @SWG\Tag(name="Usuario")
     */
    public function activarPedido(ParamFetcher $paramFetcher, User $usuario)
    {
        try{
            $valor = $paramFetcher->get('valor');
            $usuario->getNegocio()->setPedido($valor);
            $this->manager()->flush();
            return $this->apiResponse($this->getUser(),200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    // /**
    //  * @Rest\Get("/informes", name="informes", defaults={"_format":"json"})
    //  * @SWG\Response(response=200,description="devuelve los informes del usuario logueado.")
    //  * @SWG\Response(response=500,description="Hubo un problema al recuperar los informes")
    //  * @SWG\Tag(name="Usuario")
    //  */
    // public function informes(){

    // }
}
