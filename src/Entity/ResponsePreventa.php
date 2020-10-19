<?php
// src/AppBundle/Entity/User.php

namespace App\Entity;
class ResponsePreventa
{
   public $preventaId;
   public $cliente;
   public $fecha;
   public $estado;
   public $tipoComprobante;
   public $numero;
   public $puntoVenta;
   public $condicionVta;

    /** constructor userSeguimiento excel */
    public function __construct(
        $preventaId,
        $cliente,
        $fecha,
        $estado,
        $tipoComprobante = null,
        $numero = null,
        $puntoVenta = null,
        $condicionVta = null

    )
    {
        $this->preventaId = $preventaId;
        $this->cliente = $cliente;
        $this->fecha = $fecha;
        $this->estado = $estado;
        $this->tipoComprobante = $tipoComprobante;
        $this->numero =   $numero;
        $this->puntoVenta =   $puntoVenta;
        $this->condicionVta = $condicionVta;
    }

}//
