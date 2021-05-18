<?php

namespace App\Controller;

use App\Entity\Negocio;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Routing\Annotation\Route;

use Swagger\Annotations as SWG;
use App\Extensions\FileUtilitiesTrait;

/**
 * Class ApiController
 *
 * @Route("/api/negocio")
 */
class NegocioController extends RestController
{
    use FileUtilitiesTrait;
     /**
     * @Rest\Get("", name="current_negocio", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve el negocio actual.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar el negocio")
     * @SWG\Tag(name="Negocio")
     */
    public function negocioCurrent()
    {
        try
        {
            return $this->apiResponse($this->getUser()->getNegocio(),200);
        } catch (Exception $e)
        {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

     /**
     * @Rest\Get("/{negocio}", name="ver_negocio", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
     * @SWG\Tag(name="Negocio")
     */
    public function negocio( Negocio $negocio)
    {
        try
        {
            return $this->apiResponse($negocio,200);
        } catch (Exception $e)
        {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
    * @Rest\Put("/editar", name="editar_negocio", defaults={"_format":"json"})
    * @Rest\RequestParam(name="razon_social",nullable=false)
    * @Rest\RequestParam(name="condicion_iva",nullable=true)
    * @Rest\RequestParam(name="cuit_cuil",nullable=false)
    * @Rest\RequestParam(name="inicio_actividad",nullable=true)
    * @Rest\RequestParam(name="punto_vta",nullable=true)
    * @Rest\RequestParam(name="iibb",nullable=true)
    * @Rest\RequestParam(name="email",nullable=false)
    * @Rest\RequestParam(name="localidad",nullable=false)
    * @Rest\RequestParam(name="direccion",nullable=false)
    * @Rest\RequestParam(name="telefono",nullable=false)
    * @Rest\RequestParam(name="logo",nullable=true)
    * @Rest\RequestParam(name="nombre_fantasia",nullable=true)
    * @SWG\Response(response=200,description="Devuelve todas las marcas de un negocio.")
    * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio.")
    * @SWG\Tag(name="Negocio")
    */
   public function negocioEditar(ParamFetcher $paramFetcher)
   {
     try {
          $negocio = $this->getUser()->getNegocio();
          $razonSocial =$paramFetcher->get('razon_social');
          $nombreFantasia = $paramFetcher->get('nombre_fantasia');
          $condicionIva = null;
          if($negocio->getFacturaElectronica() == 'S'){
            $condicionIva = $paramFetcher->get('condicion_iva');
          }
          $cuitCuil =$paramFetcher->get('cuit_cuil');
          $inicioActividad = !empty($paramFetcher->get('inicio_actividad')) ? new \DateTime($paramFetcher->get('inicio_actividad')) : null;
          $iibb = !empty($paramFetcher->get('iibb')) ? $paramFetcher->get('iibb') : null ;
          $email = $paramFetcher->get('email');
          $puntoVta = !empty($paramFetcher->get('punto_vta')) ? intval($paramFetcher->get('punto_vta')) : null;
          $localidad =$paramFetcher->get('localidad')['localidad_id'];
          $direccion = $paramFetcher->get('direccion');
          $telefono = $paramFetcher->get('telefono');
          $logo = !empty($paramFetcher->get('logo')) ? $paramFetcher->get('logo') : null;

          /** editrar negocio*/
          $condicionIva = $condicionIva ? $this->manager()->getRepository("App:CondicionIva")->find($condicionIva) : null;
          $localidad = $this->manager()->getRepository("App:Localidad")->find($localidad);
          $negocio->setRazonSocial($razonSocial);
          $negocio->setNombreFantasia($nombreFantasia);
          $negocio->setDireccion($direccion);
          $negocio->setEmail($email);
          $negocio->setTelefono($telefono);
          $negocio->setCuitCuil($cuitCuil);
          $negocio->setCondicionIva($condicionIva);
          $negocio->setIibb($iibb);
          $negocio->setPuntoVta($puntoVta);
          $negocio->setInicioActividad($inicioActividad);
          $negocio->setDireccion($direccion);
          $negocio->setLocalidad($localidad);
          $negocio->setTelefono($telefono);
          $negocio->setLogo($logo);
          $negocio->setConfiguracion('S');

          if(empty($logo) && !empty($negocio->getLogo())){ // si tengo imagen previo y envio un null
            $this->eliminarLogo($this->getParameter('public_directory').'/uploads/'.$negocio->getLogo());
            $negocio->setLogo(NULL);
          }
          elseif(!empty($logo) && !empty($negocio->getLogo())){ // si tengo imagne previa y envio una imagen
            $this->eliminarLogo($this->getParameter('public_directory').'/uploads/'.$negocio->getLogo());
            $fileName = $this->guardarLogo($logo,$negocio->getNegocioId());//guarda la imagen en el filesystem y retorna el nombre
            $negocio->setLogo($fileName);
          }
          elseif(!empty($logo)){ // si es la primera vez que cargo la imagen
            $fileName = $this->guardarLogo($logo,$negocio->getNegocioId());//guarda la imagen en el filesystem y retorna el nombre
            $negocio->setLogo($fileName);
          }
          $this->manager()->flush();

          /** seteo el base64 de la imagen para manipular mejor la imagen en el front */
          return $this->apiResponse($negocio,200);
     } catch (\Exception $e) {
       return $this->apiResponse($e,500);
     }
   }

}
