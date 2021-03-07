<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CuentaCorriente
 *
 * @ORM\Table(name="cuenta_corriente", indexes={@ORM\Index(name="cliente_id", columns={"cliente_id"})})
 * @ORM\Entity
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
     * @ORM\Column(name="monto", type="float", precision=10, scale=0, nullable=true)
     */
    private $monto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="f_modificacion", type="datetime", nullable=true)
     */
    private $fModificacion;

    /**
     * @var \Cliente
     *
     * @ORM\ManyToOne(targetEntity="Cliente", inversedBy="cuentaCorriente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="cliente_id", nullable=false)
     * })
     */
    private $cliente;

    /**
     * One product has many movimientos. This is the inverse side.
     * @ORM\OneToMany(targetEntity="Movimiento", mappedBy="cuentaCorriente")
     * @ORM\OrderBy({"fCreacion" = "DESC"})
     */
    private $movimientos;


    public function __construct($cliente)
    {
        return $this->cliente = $cliente; 
    }

    public function getCuentaCorrienteId(): ?int
    {
        return $this->cuentaCorrienteId;
    }

    public function getMonto(): ?float
    {
        return $this->monto;
    }

    public function setMonto(float $monto): self
    {
        $this->monto = $monto;

        return $this;
    }

    public function getFModificacion(): ?\DateTimeInterface
    {
        return $this->fModificacion;
    }

    public function setFModificacion(\DateTimeInterface $fModificacion): self
    {
        $this->fModificacion = $fModificacion;

        return $this;
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function abonar($monto,$movimiento): self
    {
        $this->monto = $this->monto - (float)$monto;
        $this->movimientos[]=$movimiento;
        return $this;
    }
}
