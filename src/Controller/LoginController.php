<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Negocio;
use App\Entity\Notificacion;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;

/**
 * Class ApiController
 *
 * @Route("/api")
 */
class LoginController extends RestController
{
    // USER URI's

    /**
     * @Rest\Post("/login_check", name="user_login_check")
     *
     * @SWG\Response(response=200,description="User was logged in successfully")
     * @SWG\Response(response=500,description="User was not logged in successfully")
     * @SWG\Parameter(name="username",in="body",type="string",description="The username",schema={})
     * @SWG\Parameter(name="password",in="body",type="string",description="The password",schema={})
     * @SWG\Tag(name="User")
     */
    public function getLoginCheckAction() {
    }

    /**
     * @Rest\Post("/register", name="user_register")
     *
     * @SWG\Response(response=201,description="User was successfully registered")
     * @SWG\Response(response=500,description="User was not successfully registered")
     * @SWG\Parameter(name="name",in="body",type="string",description="The username",schema={})
     * @SWG\Parameter(name="lastname",in="body",type="string",description="The username",schema={})
     * @SWG\Parameter(name="email",in="body",type="string",description="The username",schema={})
     * @SWG\Parameter(name="username",in="body",type="string",description="The username",schema={})
     * @SWG\Parameter(name="password",in="query",type="string",description="The password")
     * @SWG\Tag(name="User")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $encoder) {
        $user = [];
        $message = "";
        try {
            $code = 200;
            $error = false;

            $name = $request->request->get('name');
            $lastname = $request->request->get('lastname');
            $email = $request->request->get('email');
            $username = $request->request->get('username');
            $password = $request->request->get('password');

            /** verifico si el usuario ya esta en el sistema **/
            $emailExistente = $this->manager()->getRepository("App:User")->findOneBy(['email'=>$email]);
            $usernameExistente = $this->manager()->getRepository("App:User")->findOneBy(['username'=>$username]);
            if(!empty($usernameExistente)){
              throw new Exception('Ya existe un usuario con ese nombre');
            }
            elseif(!empty($emailExistente)){
              throw new Exception('El email ya se encuentra registrado');
            }

            $negocio = new Negocio();
            $this->manager()->persist($negocio);

            $user = new User($name,$lastname,$username,$email,$negocio);
            $user->setPlainPassword($password);
            $user->setPassword($encoder->encodePassword($user, $password));
            $this->manager()->persist($user);

            $titulo = 'Configuración Inicial';
            $texto = 'Bienvenido a Facturea '.strtoupper($name).' '.strtoupper($lastname).'<br>';
            $texto .= 'Para empezar a utilizar nuestro sistema, deberas completar el formulario de configuración';
            $notificacion = new Notificacion($titulo,$texto,$user,'/configuracion');
            $this->manager()->persist($notificacion);

            $this->manager()->flush();
            return $this->apiResponse(['data'=>$user],200);

        } catch (Exception $e) {
          return $this->apiResponse($e->getMessage(),500);
        }

    }

    /**
     * @Rest\Get("/currentUser", name="currentUser")
    */
    public function currentUser()
    {
        $serializer = $this->get('jms_serializer');

        /** verifco si tengo logo */
        $logoUser = $this->getUser()->getNegocio()->getLogo();
        if(!empty($logoUser)){
          $extension = explode(".", $logoUser)[1];
          $logoUser = file_get_contents($this->getParameter('public_directory').'/uploads/'.$logoUser);
          $this->getUser()->getNegocio()->setLogo("data:image/".$extension.";base64,".base64_encode($logoUser));
        }
        $response = array('user' => $this->getUser());

        return new Response($serializer->serialize($response, "json"));
    }
}
