<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Condicion Venta
 *
 * @ORM\Table(name="condicion_venta")
 * @ORM\Entity
 */
  class CondicionVenta
{
    /**
     * @var int
     *
     * @ORM\Column(name="condicion_venta_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $condicionVentaId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;



    public function __construct($descripcion){
      $this->descripcion = $descripcion;
    }

    public function getCondicionVentaId(): ?int
    {
        return $this->condicionVentaId;
    }


    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

}
