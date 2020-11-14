<?php
// src/AppBundle/Entity/User.php

namespace App\Entity;
class ResponseCuentaCorriente
{

   public $preventa;
   public $fechaPreventa;
   public $montoCompra;
   public $montoDebido;
   public $fUltimoMovimiento;

    /** constructor userSeguimiento excel */
    public function __construct(
      $preventa,
      $fechaPreventa,
      $montoDebido,
      $montoCompra,
      $fUltimoMovimiento
    )
    {
      $this->preventa=$preventa;
      $this->fechaPreventa=$fechaPreventa;
      $this->montoDebido=(double)$montoDebido;
      $this->montoCompra=$montoCompra;
      $this->fUltimoMovimiento=$fUltimoMovimiento;
    }

}//
