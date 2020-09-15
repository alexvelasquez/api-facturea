<?php

namespace App\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;
use Swagger\Annotations as SWG;


class RestController extends FOSRestController
{
    protected function apiResponse($data,$code=200)
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $serializer = SerializerBuilder::create()->build();
        $response = ($code == 200  || $code == 201) ? ['code'=>$code,'data'=>$data] : ['code'=>$code,'errors'=>$data] ;
        $json = $serializer->serialize($response, 'json', $context);
        return new Response($json, $code, array(
            'Content-Type' => 'application/json'
        ));
    }

    protected function manager()
    {
        return  $this->getDoctrine()->getManager();
    }



}
