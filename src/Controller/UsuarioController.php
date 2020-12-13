<?php
namespace App\Controller;

use App\Entity\Notificacion;
use App\Entity\User;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use App\Extensions\MailUtilitiesTrait;
use Swagger\Annotations as SWG;

/**
 * Class ApiController
 *
 * @Route("/api/usuario")
 */
class UsuarioController extends RestController
{

    use MailUtilitiesTrait;
    /**
    * @Rest\get("/listado", name="listado_usuarios", defaults={"_format":"json"})
    * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
    * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
    * @SWG\Tag(name="Usuario")
    */
   public function listadoUsuarios(ParamFetcher $paramFetcher)
   {
       try
       {
          $usuarios = $this->manager()->getRepository("App:User")->findAll();
          $response = [];
          foreach ($usuarios as $usuario) {
            if($usuario->getRoles()[0] == 'ROLE_USER'){
              $response[] = $usuario;
            }
          }
          return $this->apiResponse($response,200);
       } catch (Exception $e)
       {
           return $this->apiResponse($e->getMessage(),500);
       }
   }

     /**
     * @Rest\put("/editar", name="editar_usuario", defaults={"_format":"json"})
     * @IsGranted("ROLE_USUARIO")
     * @Rest\RequestParam(name="nombre",nullable=false)
     * @Rest\RequestParam(name="apellido",nullable=false)
     * @Rest\RequestParam(name="nombreUsuario",nullable=false)
     * @Rest\RequestParam(name="email",nullable=false)
     * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
     * @SWG\Tag(name="Usuario")
     */
    public function editarUser(ParamFetcher $paramFetcher)
    {
        try
        {
            $usuario = $this->getUser();
            $usuario->setName($paramFetcher->get('nombre'));
            $usuario->setLastName($paramFetcher->get('apellido'));
            $usuario->setEmail($paramFetcher->get('email'));
            $usuario->setUsername($paramFetcher->get('nombreUsuario'));
            /** aviso al mail que se realizaron cambios en el usuario */
            $this->manager()->flush();

            return $this->apiResponse($usuario,200);
        } catch (Exception $e)
        {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
    * @Rest\put("/editarClave", name="editar_clave", defaults={"_format":"json"})
    * @Rest\RequestParam(name="actual",nullable=false)
    * @Rest\RequestParam(name="nueva",nullable=false)
    * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
    * @SWG\Response(response=500,description="Hubo un problema para editar la clave del usuario")
    * @SWG\Tag(name="Usuario")
    */
   public function editarClave(ParamFetcher $paramFetcher, UserPasswordEncoderInterface $encoder)
   {
       try
       {
           $usuario = $this->getUser();
           $nueva = $paramFetcher->get('nueva');
           $actual = $encoder->encodePassword($usuario, $paramFetcher->get('actual'));
           if($actual !== $usuario->getPassword()){
             throw new Exception('La contraseña actual es incorrecta');
           }
           $usuario->setPassword($encoder->encodePassword($usuario, $nueva));
           $this->manager()->flush();
            return $this->apiResponse($usuario,200);
       } catch (Exception $e)
       {
           return $this->apiResponse($e->getMessage(),500);
       }
   }

   /**
   * @Rest\get("/enviar", name="recuperar_clave", defaults={"_format":"json"})
   * @Rest\QueryParam(name="email",nullable=false)
   * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
   * @SWG\Response(response=500,description="Hubo un problema para editar la clave del usuario")
   * @SWG\Tag(name="User")
   */
  public function recuperarClave(ParamFetcher $paramFetcher,UserPasswordEncoderInterface $encoder,\Swift_Mailer $mailer)
  {
      try
      {
          $email = $paramFetcher->get('email');
          $usuario = $this->manager()->getRepository("App:User")->findOneBy(['email'=>$email]);
          if(empty($usuario)){
            throw new Exception('El email no existe');
          }
          $password = $this->generatePassword();
          $usuario->setPassword($encoder->encodePassword($usuario, $password));
          $this->manager()->flush();
          /** envio las credenciales por mail */
          $data['title']='Recuperar Contraseña';
          $data['destination'] = $usuario->getEmail();
          $data['url'] = 'mail/recuperarUsuario.html.twig';
          $data['usuario']= $usuario->getUsername();
          $data['clave'] = $password;
          $data['data']=$data;
          $this->sendMail($mailer,$data,[],false);
          return $this->apiResponse($usuario,200);
      } catch (Exception $e)
      {
          return $this->apiResponse($e->getMessage(),500);
      }
  }

  /**
  * @Rest\put("/pedido/{usuario}", name="pedido_usuario", defaults={"_format":"json"})
  * @Rest\RequestParam(name="valor",nullable=false)
  * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
  * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
  * @SWG\Tag(name="Usuario")
  */
 public function pedidoUser(ParamFetcher $paramFetcher,User $usuario)
 {
     try
     {
        $valor = $paramFetcher->get('valor');
        $usuario->getNegocio()->setPedidoProductos($valor);
        $titulo = 'Pedido de productos';
        $texto = 'Estimado '.strtoupper($usuario->getName()).' '.strtoupper($usuario->getLastName()).'<br>';
        if($valor == 'S'){
          $texto .= 'Ya se encuentra disponible el apartado de pedidos.';
        }
        else{
          $texto .='La baja del servicio de pedidos fue realizado correctamente';
        }
        $notificacion = new Notificacion($titulo,$texto,$usuario,'/');
        $this->manager()->persist($notificacion);
        $this->manager()->flush();
        return $this->apiResponse('Modificado',200);
     } catch (Exception $e)
     {
         return $this->apiResponse($e->getMessage(),500);
     }
 }

 /**
 * @Rest\put("/factura/{usuario}", name="factura_usuario", defaults={"_format":"json"})
 * @Rest\RequestParam(name="valor",nullable=false)
 * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
 * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
 * @SWG\Tag(name="Usuario")
 */
public function facturaUser(ParamFetcher $paramFetcher,User $usuario)
{
    try
    {
       $valor = $paramFetcher->get('valor');
       $usuario->getNegocio()->setFacturaElectronica($valor);
       $titulo = 'Factura Electrónica';
       $texto = 'Estimado '.strtoupper($usuario->getName()).' '.strtoupper($usuario->getLastName()).'<br>';
       if($valor == 'S'){
         $texto .='A partir de ahora podrá realizar facturas electrónicas. Por favor, en la sección de <strong>CONFIGURACIÓN</strong> completar los campos requeridos.';
       }
       else{
         $texto .='La baja del servicio de facturación electrónica fue realizado correctamente';
       }
       $ruta = ($valor == 'S') ? '/configuracion' : '/';
       $notificacion = new Notificacion($titulo,$texto,$usuario,$ruta);
       $this->manager()->persist($notificacion);
       $this->manager()->flush();
       return $this->apiResponse('Modificado',200);

    } catch (Exception $e)
    {
        return $this->apiResponse($e->getMessage(),500);
    }
}

  private function generatePassword(){
     $data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
     return substr(str_shuffle($data), 0,10);
  }

}
