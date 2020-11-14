<?php

namespace App\Controller;

use App\Entity\Cliente;
use App\Entity\Negocio;
use App\Entity\Preventa;
use App\Entity\Movimiento;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation\Model;
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
     * @Rest\Get("/negocio/{negocio}", name="lista_cliente", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todas los clientes de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los clientes de un negocio")
     * @SWG\Tag(name="Cliente")
     */
    public function clientesNegocio( Negocio $negocio)
    {
        try{
            $response = $this->manager()->getRepository("App:Cliente")->findBy(['negocio'=>$negocio,'fHasta'=> NULL],['razonSocial'=>'ASC']);
            foreach ($response as $cliente) {
              $montoDebido = $this->manager()->getRepository("App:Cliente")->montoDebido($cliente)['monto'] ?? 0;
              $cliente->setMontoDebido($montoDebido);
            }
            //$response = $this->manager()->getRepository("App:Cliente")->clientesNegocio($negocio);
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }


    /**
     * @Rest\Get("/cuentaCorriente/{cliente}", name="cuenta_corriente", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza el cliente de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Cliente")
     */
     public function cuentaCorrienteCliente(Cliente $cliente)
     {
       $estadoPendientePago = $this->getParameter('estado_pendiente_pago');
       $response = $this->manager()->getRepository("App:Cliente")->cuentaCorriente($cliente,$estadoPendientePago);
       return $this->apiResponse($response,200);
     }

     /**
      * @Rest\Get("/movimientos/{cliente}", name="movimientos", defaults={"_format":"json"})
      * @SWG\Response(response=200,description="Actualiza el cliente de un negocio.")
      * @SWG\Response(response=400,description="Error en los parametros")
      * @SWG\Response(response=500,description="Error en el servidor")
      * @SWG\Tag(name="Cliente")
      */
      public function movimientos(Cliente $cliente)
      {
        $response = $this->manager()->getRepository("App:Movimiento")->findBy(['cliente'=>$cliente],['fCreacion'=>'DESC']);
        return $this->apiResponse($response,200);
      }
     /**
      * @Rest\Get("/detalleCompra/{preventa}", name="detalle_compra", defaults={"_format":"json"})
      * @SWG\Response(response=200,description="Actualiza el cliente de un negocio.")
      * @SWG\Response(response=400,description="Error en los parametros")
      * @SWG\Response(response=500,description="Error en el servidor")
      * @SWG\Tag(name="Cliente")
      */
      public function detalleCompraCliente(Preventa $preventa)
      {
        $response = $this->manager()->getRepository("App:ProductoPreventa")->findBy(['preventa'=>$preventa]);
        return $this->apiResponse($response,200);
      }

      /**
       * @Rest\post("/abonar/{cliente}", name="abonar_compra", defaults={"_format":"json"})
       * @Rest\RequestParam(name="monto",nullable=false)
       * @SWG\Response(response=200,description="Actualiza el cliente de un negocio.")
       * @SWG\Response(response=400,description="Error en los parametros")
       * @SWG\Response(response=500,description="Error en el servidor")
       * @SWG\Tag(name="Cliente")
       */
       public function AbonarPagoCliente(ParamFetcher $paramFetcher, Cliente $cliente, \Swift_Mailer $mailer)
       {

         $estadoPendientePago = $this->getParameter('estado_pendiente_pago');
         $compras = $this->manager()->getRepository("App:Cliente")->comprasPendientes($cliente,$estadoPendientePago);
        //dd($compras);
         $monto = $paramFetcher->get('monto');
         foreach ($compras['cuentas'] as $value) {
           $valorCalculado = $monto - $value->montoDebido;
           $preventa = $this->manager()->getRepository("App:Preventa")->find($value->preventa);
           if($valorCalculado == 0 ){ // pago justo de un compra;
             $preventa->setMontoDebido(0);
             break;
           }
           elseif($valorCalculado < 0){ //si es menor: no llego a pagar la totalidad de la compra;
             $preventa->setMontoDebido((double)$value->montoDebido - $monto);
             break;
           }
           elseif($valorCalculado > 0){ //si es mayor: llego a pagar la totalidad de la compra y ademas le sobro para descontar otra;
             $preventa->setMontoDebido(0);
             $monto = $valorCalculado;
           }
           $preventa->setFModificacion(new \DateTime());
         }
         $movimiento = new Movimiento($cliente);
         $movimiento->setMontoPagado($monto);
         $this->manager()->persist($movimiento);
         $this->manager()->flush();
         //$this->manager->flush();
         $dataTicket = ['negocio'=>$cliente->getNegocio()->getRazonSocial(),
                        'cliente'=>$cliente->getClienteId(),
                        'transaccion'=>$movimiento->getMovimientoId(),
                        'fecha'=>date('d/m/Y H:m:s'),
                        'monto'=>$monto];
        if(!empty($cliente->getEmail())){ //si el cliente tiene mail, se manda por esa via
          $dataFile = ['url'=>'pdf/ticket.html.twig','data'=>$dataTicket]; //ticket a adjuntar en el mail
          $dataEmail = ['title'=>'Pago efectuado',
                       'destination' => $cliente->getEmail(),
                       'data'=>$cliente->getNegocio()->getRazonSocial().' le informa que el día de la fecha se efectuo el pago correctamente'
                     ];
          $this->sendMail($mailer,$dataEmail,$dataFile);
          $response = ['data'=>'Ticket enviado por mail.'];
        }
        else{
          $pdf = $this->generarPdf('pdf/ticket.html.twig',$dataTicket);
          $response = ['file' => "data:application/pdf;base64,".$pdf];
        }

        return $this->apiResponse($response,200);
       }

    /**
     * @Rest\Post("/negocio/{negocio}/nuevo", name="nuevo_cliente", defaults={"_format":"json"})
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
    public function nuevoCliente(ParamFetcher $paramFetcher, Negocio $negocio)
    {
        try {
            $errores = [];
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
            $this->manager()->flush();

            return $this->apiResponse($cliente,201);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
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
            return $this->apiResponse($ex->getMessage(),500);
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
            return $this->apiResponse($ex->getMessage(),500);
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
          return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
    * @Rest\Get("/exportar/cuentaCorriente/{cliente}", name="descargar_cuentas_corriente", defaults={"_format":"json"})
    * @SWG\Response(response=200,description="Exporta las cuentas corrientes.")
    * @SWG\Response(response=500,description="Hubo un problema para recuperar las marcas de un negocio")
    * @SWG\Tag(name="Cliente")
    */
   public function descargarPreventa(Cliente $cliente)
   {
       try
       {
           $estadoPendientePago = $this->getParameter('estado_pendiente_pago');
           $response = $this->manager()->getRepository("App:Cliente")->cuentaCorriente($cliente,$estadoPendientePago);
           $data = ['cuentas'=>$response['cuentas'],
                    'total'=>$response['total']];
           $pdfData = $this->generarPdf('pdf/cuentasCorriente.html.twig',$data);
           $response =  array('file' => "data:application/pdf;base64,".$pdfData);
           return $this->apiResponse($response,200);
       } catch (Exception $e)
       {
           return $this->apiResponse($ex->getMessage(),500);
       }
   }




}
