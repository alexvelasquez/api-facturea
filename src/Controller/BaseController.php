<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Class ApiController
 *
 * @Route("/test")
 */
class BaseController extends AbstractController
{
   /**
    * @Route("", name="asdas")
    */
    public function test()
    {
      // dd('sas');
        //return $this->render('factura.html.twig');
        return $this->render('pdf/factura.html.twig');
    }



}
