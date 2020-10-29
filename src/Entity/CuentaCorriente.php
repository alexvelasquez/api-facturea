<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuentaCorriente
 *
 * @ORM\Table(name="cuenta_corriente", indexes={@ORM\Index(name="preventa_id", columns={"preventa_id"})})
 * @ORM\Entity;
 */
class CuentaCorriente
{
    /**
     * @var int
     *
     * @ORM\Column(name="cuenta_corriente_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cuentaCorrienteId;

    /**
     * @var float
     *
     * @ORM\Column(name="monto_pagado", type="float", precision=10, scale=0, nullable=false)
     */
    private $montoPagado;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="f_creacion", type="datetimetz", nullable=false)
     */
    private $fCreacion;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="f_modificacion", type="datetimetz", nullable=false)
     */
    private $fModificacion;


    /**
     * @var \Preventa
     *
     * @ORM\ManyToOne(targetEntity="Preventa",fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="preventa_id", referencedColumnName="preventa_id",nullable=false)
     * })
     */
    private $preventa;



    public function __construct($preventa)
    {
      $this->preventa = $preventa;
      $this->montoPagado = 0;
      $this->fCreacion = new \DateTime();
      $this->fModificacion = new \DateTime();
    }

    public function getCuentaCorrienteId(): ?int
    {
        return $this->cuentaCorrienteId;
    }


    public function getFModificacion(): ?\DateTimeInterface
    {
        return $this->fModificacion;
    }

    public function setFModificacion(?\DateTimeInterface $fModificacion): self
    {
        $this->fModificacion = $fModificacion;

        return $this;
    }

    public function getFCreacion(): ?\DateTimeInterface
    {
        return $this->fCreacion;
    }

    public function setFCreacion(?\DateTimeInterface $fCreacion): self
    {
        $this->fCreacion = $fCreacion;

        return $this;
    }

    public function getPreventa(): ?Preventa
    {
        return $this->preventa;
    }

    public function setPreventa(?Preventa $preventa): self
    {
        $this->preventa = $preventa;
        return $this;
    }
}
