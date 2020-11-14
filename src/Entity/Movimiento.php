<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuentaCorriente
 *
 * @ORM\Table(name="movimiento", indexes={@ORM\Index(name="movimiento_id", columns={"movimiento_id"})})
 * @ORM\Entity;
 */
class Movimiento
{
    /**
     * @var int
     *
     * @ORM\Column(name="movimiento_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $movimientoId;

    /**
     * @var float
     *
     * @ORM\Column(name="monto_pagado", type="float", precision=10, scale=0, nullable=false)
     */
    private $montoPagado;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    private $observacion;


    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="f_creacion", type="datetimetz", nullable=false)
     */
    private $fCreacion;



    /**
     * @var \Cliente
     *
     * @ORM\ManyToOne(targetEntity="Cliente",fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="cliente_id",nullable=false)
     * })
     */
    private $cliente;



    public function __construct($cliente)
    {
      $this->cliente = $cliente;
      $this->montoPagado = 0;
      $this->fCreacion = new \DateTime();
    }

    public function getMovimientoId(): ?int
    {
        return $this->movimientoId;
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
    public function getMontoPagado(): ?float
    {
        return $this->montoPagado;
    }

    public function setMontoPagado(float $montoPagado): self
    {
        $this->montoPagado = $montoPagado;

        return $this;
    }

    public function getObservacion(): ?String
    {
        return $this->observacion;
    }

    public function setObservacion(String $observacion): self
    {
        $this->observacion = $observacion;

        return $this;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Preventa $cliente): self
    {
        $this->cliente = $cliente;
        return $this;
    }
}
