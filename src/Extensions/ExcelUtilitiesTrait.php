<?php

namespace App\Extensions;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Este trait incluye los metodos para procesar archivos
 */
trait ExcelUtilitiesTrait
{
  protected function setTextColor(&$sheet,$celda,$color)
  {
    $sheet->getStyle($celda)->getFont()->getColor()->setARGB($color);
  }
  protected function setAjustarTextCelda(&$sheet,$celda)
  {
    $sheet->getColumnDimension($celda)->setAutoSize(true);
  }
  protected function setCellsColor(&$sheet,$celdas,$color)
  {
    $sheet->getStyle($celdas)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB($color);
  }
  protected function setHeight(&$sheet,$celda,$size)
  {
    $sheet->getRowDimension($celda)->setRowHeight($size);
  }
  protected function setAligment(&$sheet,$celdas,$align)
  {
    $sheet->getStyle($celdas)->getAlignment()->setHorizontal($align);
  }


}
