<?php

namespace App\Extensions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
/**
 * Este trait incluye los metodos para procesar archivos
 */
trait FileUtilitiesTrait
{
  /** Proceso la imagen en base 64 y la guardo en el file system*/
    protected function guardarLogo(String $img, $name)
    {
      try{
        $dataImagen=  explode(';base64,',$img);
        $extension =  explode('/',$dataImagen[0])[1]; //me quedo con la extension
        $name = MD5($name).'.'.$extension;
        $imagenBase64 = $dataImagen[1]; //imagen en base 64
        $base64Image = base64_decode($imagenBase64);
        file_put_contents($this->getParameter('public_directory').'/uploads/'.$name,$base64Image);
        return $name;//guardo la imagen en el filesystem
      }
      catch(\Exception $e){
         throw new Exception($e);
      }
    }
    protected function eliminarLogo($img){
      $filesystem = new Filesystem();
      $filesystem->remove($img);
    }
    protected function verifyNewImage(String $img)
    {
        return !empty(strpos($img, ';base64,'));
    }
}
