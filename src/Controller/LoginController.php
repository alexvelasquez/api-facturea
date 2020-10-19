<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Negocio;
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
class LoginController extends FOSRestController
{
    // USER URI's

    /**
     * @Rest\Post("/login_check", name="user_login_check")
     *
     * @SWG\Response(response=200,description="User was logged in successfully")
     * @SWG\Response(response=500,description="User was not logged in successfully")
     * @SWG\Parameter(name="_username",in="body",type="string",description="The username",schema={})
     * @SWG\Parameter(name="_password",in="body",type="string",description="The password",schema={})
     * @SWG\Tag(name="User")
     */
    public function getLoginCheckAction() {
    }

    /**
     * @Rest\Post("/register", name="user_register")
     *
     * @SWG\Response(response=201,description="User was successfully registered")
     * @SWG\Response(response=500,description="User was not successfully registered")
     * @SWG\Parameter(name="_name",in="body",type="string",description="The username",schema={})
     * @SWG\Parameter(name="_email",in="body",type="string",description="The username",schema={})
     * @SWG\Parameter(name="_username",in="body",type="string",description="The username",schema={})
     * @SWG\Parameter(name="_password",in="query",type="string",description="The password")
     * @SWG\Tag(name="User")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $encoder) {
        $serializer = $this->get('jms_serializer');
        $em = $this->getDoctrine()->getManager();

        $user = [];
        $message = "";
        try {
            $code = 200;
            $error = false;

            $name = $request->request->get('_name');
            $email = $request->request->get('_email');
            $username = $request->request->get('_username');
            $password = $request->request->get('_password');

            $negocio = new Negocio();
            $user = new User();
            $user->setName($name);
            $user->setEmail($email);
            $user->setUsername($username);
            $user->setPlainPassword($password);
            $user->setPassword($encoder->encodePassword($user, $password));
            $user->setFacturaElectronica('N');
            $user->setNegocio($negocio);
            $em->persist($negocio);
            $em->persist($user);
            $em->flush();

        } catch (Exception $ex) {
            $code = 500;
            $error = true;
            $message = "An error has occurred trying to register the user - Error: {$ex->getMessage()}";
        }

        $response = [
            'code' => $code,
            'error' => $error,
            'data' => $code == 200 ? $user : $message,
        ];

        return new Response($serializer->serialize($response, "json"));
    }

    /**
     * @Rest\Get("/currentUser", name="currentUser")
    */
    public function currentUser()
    {
        $serializer = $this->get('jms_serializer');

        /** verifco si tengo logo */
        $logoUser = $this->getUser()->getNegocio()->getLogo();
        $extension = explode(".", $logoUser)[1];
        if(!empty($logoUser)){
          $logoUser = file_get_contents($this->getParameter('public_directory').'/uploads/'.$logoUser);
          $this->getUser()->getNegocio()->setLogo("data:image/".$extension.";base64,".base64_encode($logoUser));
        }
        $response = array('user' => $this->getUser());

        return new Response($serializer->serialize($response, "json"));
    }
}
