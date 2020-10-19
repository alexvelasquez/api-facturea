<?php

namespace App\Extensions;

use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Picqer\Barcode\BarcodeGeneratorHTML;


/**
 * Este trait incluye los metodos para procesar archivos
 */
trait PDFUtilitiesTrait
{

  private function generarPdf($url,$datos){
    /* pdf/factura.html.twig**/
    $html = $this->renderView($url,$datos);

    $options = new Options();
    $options->set('isRemoteEnabled', TRUE);
    $options->setIsHtml5ParserEnabled(true);
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    return base64_encode($dompdf->output());

  }

}
