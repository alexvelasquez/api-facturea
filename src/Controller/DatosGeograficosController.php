<?php

namespace App\Controller;

use App\Entity\Provincia;
use App\Entity\Localidad;
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
use App\Service\DatosGeograficosService;


/**
 * Class ApiController
 *
 * @Route("/api/datosGeograficos")
 */
class DatosGeograficosController extends RestController
{
     /**
     * @Rest\Get("/provincias", name="lista_provincias", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas las provincias ordenadas por nombre.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar las provincias")
     * @SWG\Tag(name="Datos Geográficos")
     */
    public function provincias()
    {
        $dataProvincias =  $this->manager()->getRepository("App:Provincia")->findAll();
        return $this->apiResponse($dataProvincias,200);
    }

    /**
     * @Rest\Get("/localidades/{provincia}", name="lista_localidades", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas las localidades ordenadas por nombre.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar las localidades")
     * @SWG\Tag(name="Datos Geográficos")
     */
    public function localidades(Provincia $provincia)
    {
        $dataLocalidades = $this->manager()->getRepository("App:Localidad")->findBy(['provincia'=>$provincia]);
        return $this->apiResponse($dataLocalidades,200);
    }

    /**
     * @Rest\Post("/cargar", name="add_provincias", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas las localidades ordenadas por nombre.")
     * @SWG\Response(response=400,description="Hubo un problema para recuperar las localidades")
     * @SWG\Tag(name="Datos Geográficos")
     */
    public function cargar(DatosGeograficosService $service)
    {
      $provincias = $service->get('/provincias')->data->provincias;//['provincias'];
      foreach ($provincias as $value) {
          $provincia = new Provincia($value->id,$value->nombre);
          $this->manager()->persist($provincia);
      }
      $this->manager()->flush();
      $localidades = $service->get('/localidades?campos=nombre,provincia&max=5000')->data->localidades;//['provincias'];
      foreach ($localidades as $value) {
          $provinciaLocalidad = $value->provincia->id;
          $provincia = $this->manager()->getRepository("App:Provincia")->findOneBy(['geoId'=>$provinciaLocalidad]);
          $localidad = new Localidad($value->nombre,$provincia);
          $this->manager()->persist($localidad);
      }
      $this->manager()->flush();
      return $this->apiResponse(['Agregado Correctamente'],200);
    }

}
