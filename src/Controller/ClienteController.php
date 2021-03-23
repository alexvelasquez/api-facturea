<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\CuentaCorriente;
use App\Entity\Negocio;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use Symfony\Component\Config\Definition\Exception\Exception;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use App\Extensions\PDFUtilitiesTrait;
use App\Extensions\MailUtilitiesTrait;

/**
 * Class ApiController
 *
 * @Route("/api/clientes")
 */
class ClienteController extends RestController
{

    use PDFUtilitiesTrait;
    use MailUtilitiesTrait;
     /**
     * @Rest\Get("", name="lista_cliente", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas los clientes de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los clientes de un negocio")
     * @SWG\Tag(name="Cliente")
     */
    public function clientesNegocio()
    {
        try{
            $negocio = $this->getUser()->getNegocio();
            $response = $this->manager()->getRepository("App:Cliente")->findBy(['negocio'=>$negocio,'fHasta'=> NULL],['razonSocial'=>'ASC']);
            //$response = $this->manager()->getRepository("App:Cliente")->clientesNegocio($negocio);
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Post("/nuevo", name="nuevo_cliente", defaults={"_format":"json"})
     * @Rest\RequestParam(name="razon_social",nullable=false)
     * @Rest\RequestParam(name="email",nullable=true)
     * @Rest\RequestParam(name="localidad",nullable=false)
     * @Rest\RequestParam(name="direccion",nullable=true)
     * @Rest\RequestParam(name="tipo_documento",nullable=false)
     * @Rest\RequestParam(name="documento",nullable=true)
     * @Rest\RequestParam(name="condicion_iva",nullable=false)
     * @Rest\RequestParam(name="telefono",nullable=true)
     * @SWG\Response(response=201,description="Producto creado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear el cliente")
     * @SWG\Parameter(name="razonSocial",in="body",type="string",description="razonSocial del cliente",schema={})
     * @SWG\Parameter(name="email",in="body",type="string",description="email del cliente",schema={})
     * @SWG\Parameter(name="provincia",in="body",type="number",description="sotck del cliente",schema={})
     * @SWG\Parameter(name="localidad",in="body",type="number",description="localidad del cliente",schema={})
     * @SWG\Parameter(name="direccion",in="body",type="number",description="direccion del cliente",schema={})
     * @SWG\Parameter(name="tipoDoc",in="body",type="number",description="tipoDoc del cliente",schema={})
     * @SWG\Parameter(name="documento",in="body",type="string",description="documento del cliente",schema={})
     * @SWG\Parameter(name="condIva",in="body",type="string",description="condIva del cliente",schema={})
     * @SWG\Tag(name="Cliente")
     */
    public function nuevoCliente(ParamFetcher $paramFetcher)
    {
        try {
            $errores = [];

            /** ALTA DE CLIENTE */
            $negocio = $this->getUser()->getNegocio();
            $razonSocial = $paramFetcher->get('razon_social');
            $email  = $paramFetcher->get('email');
            $localidad  = $paramFetcher->get('localidad')['localidad_id'];
            $direccion = $paramFetcher->get('direccion') ?? null;
            $tipoDocumento = $paramFetcher->get('tipo_documento')['tipo_documento_id'];
            $documento = $paramFetcher->get('documento') ?? 0;
            $condicionIva = $paramFetcher->get('condicion_iva')['condicion_iva_id'];
            $telefono = $paramFetcher->get('telefono') ?? null;

            $localidad = $this->manager()->getRepository("App:Localidad")->find($localidad);
            $tipoDocumento = $this->manager()->getRepository("App:TipoDocumento")->find($tipoDocumento);
            $condicionIva = $this->manager()->getRepository("App:CondicionIva")->find($condicionIva);

            $cliente = new Cliente($razonSocial,$email,$localidad,$direccion,$telefono,$tipoDocumento,$documento,$condicionIva,$negocio);
            $this->manager()->persist($cliente);

            /** ALTA DE CUENTA CORRIENTE DE ESE CLIENTE */
            $cuentaCorriente = new CuentaCorriente($cliente);
            $this->manager()->persist($cuentaCorriente);
            $this->manager()->flush();

            return $this->apiResponse($cliente,201);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/editar/{cliente}", name="editar_cliente", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza la cliente de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     *
     * @SWG\Parameter(name="razonSocial",in="body",type="string",description="razonSocial del cliente",schema={})
     * @SWG\Parameter(name="email",in="body",type="string",description="email del cliente",schema={})
     * @SWG\Parameter(name="provincia",in="body",type="number",description="sotck del cliente",schema={})
     * @SWG\Parameter(name="localidad",in="body",type="number",description="localidad del cliente",schema={})
     * @SWG\Parameter(name="direccion",in="body",type="number",description="direccion del cliente",schema={})
     * @SWG\Parameter(name="tipoDoc",in="body",type="number",description="tipoDoc del cliente",schema={})
     * @SWG\Parameter(name="documento",in="body",type="string",description="documento del cliente",schema={})
     * @SWG\Parameter(name="condIva",in="body",type="string",description="condIva del cliente",schema={})
     * @SWG\Tag(name="Cliente")
     */
    public function editarCliente(Request $request, Cliente $cliente )
    {
        try {
            $errores = [];
            !empty($request->request->get('razon_social')) ? $razonSocial = $request->request->get('razon_social')    :  $errores['razon_social'] = 'Este campo es obligatorio';
            !empty($request->request->get('email'))  ? $email  = $request->request->get('email')     :  $errores['email'] = 'Este campo es obligatorio';
            !empty($request->request->get('localidad'))  ? $localidad  = $request->request->get('localidad')['localidad_id']     :  $errores['localidad'] = 'Este campo es obligatorio';
            !empty($request->request->get('direccion')) ? $direccion = $request->request->get('direccion') :  $errores['direccion'] = 'Este campo es obligatorio';
            !empty($request->request->get('tipo_documento')) ? $tipoDocumento = $request->request->get('tipo_documento')['tipo_documento_id']    :  $errores['tipo_documento'] = 'Este campo es obligatorio' ;
            !empty($request->request->get('documento')) ? $documento = $request->request->get('documento')    :  $errores['documento'] = 'Este campo es obligatorio' ;
            !empty($request->request->get('condicion_iva')) ? $condicionIva = $request->request->get('condicion_iva')['condicion_iva_id']    :  $errores['condicion_iva'] = 'Este campo es obligatorio' ;
            !empty($request->request->get('telefono')) ? $telefono = $request->request->get('telefono') :  $errores['telefono'] = 'Este campo es obligatorio' ;
            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            $localidad = $this->manager()->getRepository("App:Localidad")->find($localidad);
            $tipoDocumento = $this->manager()->getRepository("App:TipoDocumento")->find($tipoDocumento);
            $condicionIva = $this->manager()->getRepository("App:CondicionIva")->find($condicionIva);

            /** Actualizo los campos del producto */
            $cliente->setRazonSocial($razonSocial);
            $cliente->setEmail($email);
            $cliente->setLocalidad($localidad);
            $cliente->setDireccion($direccion);
            $cliente->setTipoDocumento($tipoDocumento);
            $cliente->setDocumento($documento);
            $cliente->setCondicionIva($condicionIva);
            $cliente->setTelefono($telefono);
              //return $this->apiResponse($telefono,200);
            $cliente->setFModificacion(new \DateTime());
            $this->manager()->flush();

            return $this->apiResponse($cliente,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }


    /**
     * @Rest\Put("/eliminar/{cliente}", name="eliminar_cliente", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza el cliente de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Cliente")
     */
    public function eliminarCliente(Cliente $cliente )
    {

        try {
            /** Actualizo los campos del producto */
            $cliente->setFModificacion(new \DateTime());
            $cliente->setFHasta(new \DateTime());
            $this->manager()->flush();

            return $this->apiResponse($cliente,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/eliminarClientes", name="eliminar_clientes", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza el cliente de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Cliente")
     */
    public function eliminarClientes(Request $request)
    {
        $errores = [];

        !empty($request->request->get('clientes')) ? $clientes = json_decode($request->request->get('clientes')) : $errores['clientes'] = 'Este campo es obligatorio' ;
        if(!empty($errores))
        {
          return $this->apiResponse($errores,400);
        }
        try
        {
          /** begin transaccion */
          $this->manager()->getConnection()->beginTransaction();
          foreach ($clientes as $p)
          {
              $cliente = $this->manager()->getRepository("App:Cliente")->find($p->cliente_id);
              /** Actualizo los campos del producto */
              $cliente->setFModificacion(new \DateTime());
              $cliente->setFHasta(new \DateTime());
              $this->manager()->flush();
          }
          /** end transaccion */
          $this->manager()->getConnection()->commit();
          return $this->apiResponse([],200);

        } catch (Exception $e) {
          /** rollback transaccion */
          $this->manager()->getConnection()->rollback();
          return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Get("/compras/{cliente}", name="compras_cliente", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Cuenta corriente cliente.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Cliente")
     */
    public function compras(Cliente $cliente)
    {
        try{
            $compras = $this->manager()->getRepository("App:Venta")->compras($cliente);
            return $this->apiResponse($compras,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Get("/cuentaCorriente/{cliente}", name="cuenta_corriente_cliente", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Cuenta corriente cliente.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Cliente")
     */
    public function cuentaCorriente(Cliente $cliente)
    {
        try{
            $response = $this->manager()->getRepository("App:CuentaCorriente")->findOneBy(['cliente'=>$cliente],['fModificacion'=>'DESC']);
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
        
    }


}
