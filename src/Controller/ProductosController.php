<?php

namespace App\Controller;

use App\Entity\Producto;
use App\Entity\Negocio;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcher;

use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;

// Include PhpSpreadsheet required namespaces
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Extensions\ExcelUtilitiesTrait;
use Symfony\Component\Filesystem\Filesystem;
/**
 * Class ApiController
 *
 * @Route("/api/productos")
 */
class ProductosController extends RestController
{

    use ExcelUtilitiesTrait;
     /**
     * @Rest\Get("/negocio", name="lista_productos", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Devuelve todo los productos de un negocio.")
     * @SWG\Response(response=500,description="Hubo un problema para recuperar los productos de un negocio")
     * @SWG\Tag(name="Producto")
     */
    public function productosNegocio()
    {
        try{
            $negocio = $this->getUser()->getNegocio();
            $response = $this->manager()->getRepository("App:Producto")->findBy(['negocio'=>$negocio,'fHasta'=>NULL]);
            return $this->apiResponse($response,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Post("/nuevo", name="nuevo_producto", defaults={"_format":"json"})
     * @Rest\RequestParam(name="descripcion",nullable=false)
     * @Rest\RequestParam(name="codigo",nullable=false)
     * @Rest\RequestParam(name="stock",nullable=false)
     * @Rest\RequestParam(name="categoria",nullable=false)
     * @Rest\RequestParam(name="marca",nullable=false)
     * @Rest\RequestParam(name="precio_compra",nullable=false)
     * @Rest\RequestParam(name="aumento",nullable=false)
     * @SWG\Response(response=201,description="Producto creado correctamente")
     * @SWG\Response(response=400,description="Ha ocurrido un error en los parametros")}
     * @SWG\Response(response=500,description="Ha ocurrido un error al crear el producto")
     * @SWG\Parameter(name="nombre",in="body",type="string",description="nombre del producto",schema={})
     * @SWG\Parameter(name="stock",in="body",type="number",description="stock del producto",schema={})
     * @SWG\Parameter(name="marca",in="body",type="number",description="marca del producto",schema={})
     * @SWG\Parameter(name="categoria",in="body",type="number",description="categoria del producto",schema={})
     * @SWG\Parameter(name="precio",in="body",type="number",description="precio del producto",schema={})
     * @SWG\Parameter(name="incremento",in="body",type="string",description="incremento (porcentaje)",schema={})
     * @SWG\Tag(name="Producto")
     */
    public function nuevoProducto(Request $request)
    {
        try {
            $negocio = $this->getUser()->getNegocio();
            $descripcion = $request->request->get('descripcion');
            $codigo  = $request->request->get('codigo');
            $stock  = $request->request->get('stock');
            $categoria  = $request->request->get('categoria')['categoria_id'];
            $marca  = $request->request->get('marca')['marca_id'];
            $precioCompra = $request->request->get('precio_compra');
            $aumento = $request->request->get('aumento');
            $categoria = $this->manager()->getRepository("App:Categoria")->find($categoria);
            $marca = $this->manager()->getRepository("App:Marca")->find($marca);
            $producto = new Producto($descripcion,$codigo,$stock,$categoria,$marca,$precioCompra,$aumento,$negocio);

            $this->manager()->persist($producto);
            $this->manager()->flush();

            return $this->apiResponse($producto,201);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/editar/{producto}", name="editar_producto", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza el producto de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     *
     * @SWG\Parameter(name="nombre",in="body",type="string",description="nombre del producto",schema={})
     * @SWG\Parameter(name="codigo",in="body",type="string",description="codigo del producto",schema={})
     * @SWG\Parameter(name="stock",in="body",type="number",description="sotck del producto",schema={})
     * @SWG\Parameter(name="marca",in="body",type="number",description="marca del producto",schema={})
     * @SWG\Parameter(name="categoria",in="body",type="number",description="categoria del producto",schema={})
     * @SWG\Parameter(name="precio",in="body",type="number",description="precio del producto",schema={})
     * @SWG\Parameter(name="incremento",in="body",type="string",description="incremento (porcentaje)",schema={})
     * @SWG\Tag(name="Producto")
     */
    public function editarProducto(Request $request, Producto $producto )
    {

        try {
            $errores = [];
            !empty($request->request->get('descripcion')) ? $descripcion = $request->request->get('descripcion')    :  $errores['descripcion'] = 'Este campo es obligatorio';
            !empty($request->request->get('codigo'))  ? $codigo  = $request->request->get('codigo')     :  $errores['codigo'] = 'Este campo es obligatorio';
            !empty($request->request->get('stock'))  ? $stock  = $request->request->get('stock')     :  $errores['stock'] = 'Este campo es obligatorio';
            !empty($request->request->get('categoria'))  ? $categoria  = $request->request->get('categoria')['categoria_id']    :  $errores['categoria'] = 'Este campo es obligatorio';
            !empty($request->request->get('marca'))  ? $marca  = $request->request->get('marca')['marca_id']   :  $errores['marca'] = 'Este campo es obligatorio';
            !empty($request->request->get('precio_compra')) ? $precioCompra = $request->request->get('precio_compra')    :  $errores['precio_compra'] = 'Este campo es obligatorio' ;
            !empty($request->request->get('aumento')) ? $aumento = $request->request->get('aumento')    :  $errores['aumento'] = 'Este campo es obligatorio' ;
            if(!empty($errores))
            {
                return $this->apiResponse($errores,400);
            }
            $categoria = $this->manager()->getRepository("App:Categoria")->find($categoria);
            $marca = $this->manager()->getRepository("App:Marca")->find($marca);
            /** Actualizo los campos del producto */
            $producto->setDescripcion($descripcion);
            $producto->setCodigo($codigo);
            $producto->setStock($stock);
            $producto->setCategoria($categoria);
            $producto->setMarca($marca);
            $producto->setPrecioCompra($precioCompra);
            $producto->setAumento($aumento);
            $producto->setFModificacion(new \DateTime());

            $this->manager()->flush();

            return $this->apiResponse($producto,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/eliminar/{producto}", name="eliminar_producto", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza el producto de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Producto")
     */
    public function eliminarProducto(Producto $producto )
    {

        try {
            /** Actualizo los campos del producto */
            $producto->setFModificacion(new \DateTime());
            $producto->setFHasta(new \DateTime());
            $this->manager()->flush();

            return $this->apiResponse($producto,200);
        } catch (Exception $e) {
            return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Put("/eliminarProductos", name="eliminar_producto", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Actualiza el producto de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Producto")
     */
    public function eliminarProductos(Request $request)
    {
        $errores = [];

        !empty($request->request->get('productos')) ? $productos = json_decode($request->request->get('productos')) : $errores['productos'] = 'Este campo es obligatorio' ;
        if(!empty($errores))
        {
          return $this->apiResponse($errores,400);
        }
        try
        {
          /** begin transaccion */
          $this->manager()->getConnection()->beginTransaction();
          foreach ($productos as $p)
          {
              $producto = $this->manager()->getRepository("App:Producto")->find($p->producto_id);
              /** Actualizo los campos del producto */
              $producto->setFModificacion(new \DateTime());
              $producto->setFHasta(new \DateTime());
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
     * @Rest\Get("/negocio/exportar", name="exportar_productos", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Exportar los productos de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Producto")
     */
    public function exportarProductos()
    {
        try
        {
          $negocio = $this->getUser()->getNegocio();
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();
          $sheet->setCellValue('A1', 'NOMBRE PRODUCTO');
          $sheet->setCellValue('B1', 'CÓDIGO');
          $sheet->setCellValue('C1', 'STOCK');
          $sheet->setCellValue('D1', 'PRECIO NETO');
          $sheet->setCellValue('E1', 'MARCA');
          $sheet->setCellValue('F1', 'CATEGORIA');
          $this->setCellsColor($sheet,'A1:F1','385F73');
          foreach(range('A','F') as $columnID) {
             $celda = $columnID."1";
             $this->setAjustarTextCelda($sheet,$columnID);
             $this->setTextColor($sheet,$celda,'FFFFFFFF');
             $this->setHeight($sheet,'1',20);

          }
          $indice = 2;
          $productos = $this->manager()->getRepository("App:Producto")->findBy(['negocio'=>$negocio,'fHasta'=>NULL]);
          foreach ($productos as $value) {
            $sheet->setCellValue('A'.$indice, strtoupper($value->getDescripcion()));
            $sheet->setCellValue('B'.$indice, $value->getCodigo());
            $sheet->setCellValue('C'.$indice, $value->getStock());
            $sheet->setCellValue('D'.$indice, $value->getPrecioPublicado());
            $sheet->setCellValue('E'.$indice, strtoupper($value->getMarca()->getMarcaId()));
            $sheet->setCellValue('F'.$indice, strtoupper($value->getCategoria()->getCategoriaId()));
            $this->setHeight($sheet,$indice,20);
            $indice ++;
          }
          $writer = new Xlsx($spreadsheet);
          ob_start();
          $writer->save("php://output");
          $xlsData = ob_get_contents();
          ob_end_clean();
          $response =  array(
              'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
          );
          return $this->apiResponse($response,200);

        } catch (Exception $e) {
          return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Get("/exportar_ejemplo", name="exportar_ejemplo", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Exportar los productos de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Producto")
     */
    public function exportarEjemplo(Request $request)
    {
        try
        {
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();
          $sheet->setCellValue('A1', 'DESCRIPCIÓN');
          $sheet->setCellValue('B1', 'CÓDIGO');
          $sheet->setCellValue('C1', 'STOCK');
          $sheet->setCellValue('D1', 'PRECIO COMPRA');
          $sheet->setCellValue('E1', 'AUMENTO(%)');
          $sheet->setCellValue('F1', 'MARCA(código de marca)');
          $sheet->setCellValue('G1', 'CATEGORIA(código de categoria)');
          $this->setCellsColor($sheet,'A1:G1','385F73');
          foreach(range('A','G') as $columnID) {
             $celda = $columnID."1";
             $this->setAjustarTextCelda($sheet,$columnID);
             $this->setTextColor($sheet,$celda,'FFFFFFFF');
             $this->setHeight($sheet,'1',20);
          }

          $ejemplos = array(['descripcion'=>'CORTADORA CESPED',
                            'codigo'=>'1000',
                            'stock'=>'80',
                            'precio_compra'=>1550.2,
                            'aumento'=>'40',
                            'marca'=>'1',
                            'categoria'=>'1'],
                            ['descripcion'=>'TIJERA DE PODAR',
                            'codigo'=>'10001',
                            'stock'=>'150',
                            'precio_compra'=>750,
                            'aumento'=>'25',
                            'marca'=>'2',
                            'categoria'=>'1'],
                            ['descripcion'=>'MOTOSIERRA',
                            'codigo'=>'10002',
                            'stock'=>'50',
                            'precio_compra'=>15982.30,
                            'aumento'=>'35',
                            'marca'=>'3',
                            'categoria'=>'1']);

        $indice = 2;
        foreach ($ejemplos as $value) {
          $sheet->setCellValue('A'.$indice, $value['descripcion']);
          $sheet->setCellValue('B'.$indice, $value['codigo']);
          $sheet->setCellValue('C'.$indice, $value['stock']);
          $sheet->setCellValue('D'.$indice, $value['precio_compra']);
          $sheet->setCellValue('E'.$indice, $value['aumento']);
          $sheet->setCellValue('F'.$indice, $value['marca']);
          $sheet->setCellValue('G'.$indice, $value['categoria']);
          $this->setHeight($sheet,$indice,20);
          $indice ++;
        }
        $writer = new Xlsx($spreadsheet);
        ob_start();
        $writer->save("php://output");
        $xlsData = ob_get_contents();
        ob_end_clean();
          $response =  array(
              'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
          );
          return $this->apiResponse($response,200);

        } catch (Exception $e) {
          return $this->apiResponse($ex->getMessage(),500);
        }
    }


    /**
     * @Rest\Get("/exportar_modelo", name="exportar_modelo", defaults={"_format":"json"})
     * @SWG\Response(response=200,description="Exportar los productos de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Producto")
     */
    public function exportarModelo(Request $request)
    {
        try
        {
          $spreadsheet = new Spreadsheet();
          $sheet = $spreadsheet->getActiveSheet();
          $sheet->setCellValue('A1', 'DESCRIPCIÓN');
          $sheet->setCellValue('B1', 'CÓDIGO');
          $sheet->setCellValue('C1', 'STOCK');
          $sheet->setCellValue('D1', 'PRECIO COMPRA');
          $sheet->setCellValue('E1', 'AUMENTO(%)');
          $sheet->setCellValue('F1', 'MARCA(código de marca)');
          $sheet->setCellValue('G1', 'CATEGORIA(código de categoria)');
          $this->setCellsColor($sheet,'A1:G1','385F73');
          foreach(range('A','G') as $columnID) {
             $celda = $columnID."1";
             $this->setAjustarTextCelda($sheet,$columnID);
             $this->setTextColor($sheet,$celda,'FFFFFFFF');
             $this->setHeight($sheet,'1',20);
          }
          $writer = new Xlsx($spreadsheet);
          ob_start();
          $writer->save("php://output");
          $xlsData = ob_get_contents();
          ob_end_clean();
            $response =  array(
                'file' => "data:application/vnd.ms-excel;base64,".base64_encode($xlsData)
            );
          return $this->apiResponse($response,200);

        } catch (Exception $e) {
          return $this->apiResponse($ex->getMessage(),500);
        }
    }

    /**
     * @Rest\Post("/negocio/{negocio}/importar_excel", name="importar", defaults={"_format":"json"})
     * @Rest\FileParam(name="file",nullable=false)
     * @SWG\Response(response=200,description="Exportar los productos de un negocio.")
     * @SWG\Response(response=400,description="Error en los parametros")
     * @SWG\Response(response=500,description="Error en el servidor")
     * @SWG\Tag(name="Producto")
     */
    public function importarModelo(ParamFetcher $paramFetcher,Negocio $negocio)
    {
        try
        {
          $files = $paramFetcher->get('file');
          /** Renombro el archivo temporal con .xlsx*/
          $dir = str_replace('.tmp','.xlsx',$files->getPathName());
          rename($files->getPathName(),$dir);
          /** cargo el xlsx para leerlo con la libreria PhpSpreadsheet */
          $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($dir);
          $sheet = $spreadsheet->getActiveSheet();
          $filas = $sheet->getHighestRow();/** cantidad de $filas */
          foreach(range(2,$filas) as $indice) {
             $errores = 0;
             !empty($sheet->getCell('A'.$indice)->getValue()) ? $descripcion =  $sheet->getCell('A'.$indice)->getValue() : $errores++;
             !empty($sheet->getCell('B'.$indice)->getValue()) ? $codigo = $sheet->getCell('B'.$indice)->getValue() : $errores++;
             !empty($sheet->getCell('C'.$indice)->getValue()) ? $stock =  $sheet->getCell('C'.$indice)->getValue() : $errores++;
             !empty($sheet->getCell('D'.$indice)->getValue()) ? $precioCompra = $sheet->getCell('D'.$indice)->getValue() : $errores++;
             !empty($sheet->getCell('E'.$indice)->getValue()) ? $aumento = $sheet->getCell('E'.$indice)->getValue() : $errores++;
             !empty($sheet->getCell('F'.$indice)->getValue()) ? $marca = $sheet->getCell('F'.$indice)->getValue() : $errores++;
             !empty($sheet->getCell('G'.$indice)->getValue()) ? $categoria = $sheet->getCell('G'.$indice)->getValue() : $errores++;
             if($errores>0){
               throw new Exception('Los campos en el archivo no pueden estar vacios.');
             }
             $nroMarca = $sheet->getCell('F'.$indice)->getValue();
             $nroCategoria = $sheet->getCell('G'.$indice)->getValue();
             $marca = $this->manager()->getRepository("App:Marca")->findOneBy(['codigo'=>$nroMarca,'negocio'=>$negocio]);
             $categoria = $this->manager()->getRepository("App:Categoria")->findOneBy(['codigo'=>$nroCategoria,'negocio'=>$negocio]);
             if(empty($marca) || empty($categoria)){
               throw new Exception('Los codigos de marca o categoria no son correctos');
             }
             $producto = new Producto($descripcion, $codigo, $stock, $categoria,$marca, $precioCompra, $aumento,$negocio );
             $this->manager()->persist($producto);
          }
          /** elimino el archivo temporal **/
          $filesystem = new Filesystem();
          $filesystem->remove($dir);
          $this->manager()->flush();
          return $this->apiResponse('Productos cargados correctamente',204);
        } catch (Exception $e) {
            return $this->apiResponse($e->getMessage(),500);
        }
    }
}
