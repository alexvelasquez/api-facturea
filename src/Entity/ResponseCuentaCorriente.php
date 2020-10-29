<?php
// src/AppBundle/Entity/User.php

namespace App\Entity;
class ResponseCuentaCorriente
{
   public $cuentaCorrienteId;
   public $preventa;
   public $fechaPreventa;
   public $montoDebido;
   public $montoPagado;
   public $fUltimoMovimiento;

    /** constructor userSeguimiento excel */
    public function __construct(
      $cuentaCorrienteId,
      $preventa,
      $fechaPreventa,
      $montoDebido,
      $montoPagado,
      $fUltimoMovimiento

    )
    {
      $this->cuentaCorrienteId = $cuentaCorrienteId;
      $this->preventa=$preventa;
      $this->fechaPreventa=$fechaPreventa;
      $this->montoDebido=$montoDebido;
      $this->montoPagado=$montoPagado;
      $this->fUltimoMovimiento=$fUltimoMovimiento;
    }

}//
