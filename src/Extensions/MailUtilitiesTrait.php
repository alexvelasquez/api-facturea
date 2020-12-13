<?php

namespace App\Extensions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Este trait incluye los metodos para procesar archivos
 */
trait MailUtilitiesTrait
{
  private function sendMail($mailer,$dataMail = [],$dataFile = [],$textPlain=true){

    if(!empty($dataFile)){ /** armo el pdf a adjuntar **/
      $html = $this->renderView($dataFile['url'],$dataFile['data']);
      $options = new Options();
      $options->set('isRemoteEnabled', TRUE);
      $options->setIsHtml5ParserEnabled(true);
      $dompdf = new Dompdf($options);
      $dompdf->loadHtml($html);
      $dompdf->setPaper('A4', 'portrait');
      $dompdf->render();
      $pdf = $dompdf->output();

      // Create the attachment with your data
      $attachment = new \Swift_Attachment($pdf, 'comprobante_pago.pdf', 'application/pdf');
    }

    $message = (new \Swift_Message($dataMail['title']))
         ->setFrom('info.facturea@gmail.com')//se harcodea por defecto el de facturea
         ->setTo($dataMail['destination']);
    if($textPlain){
      $message->setBody($dataMail['body'],'text/html');
    }
    else{
      $message->setBody($this->renderView($dataMail['url'],$dataMail['data']),'text/html');
    }
    if(!empty($dataFile)){
      $message->attach($attachment);
    }
    $mailer->send($message);
  }

}
