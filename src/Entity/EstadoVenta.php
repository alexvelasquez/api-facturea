<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VentaEstado
 *
 * @ORM\Table(name="estado_venta", indexes={@ORM\Index(name="estado_id", columns={"estado_id"}), @ORM\Index(name="venta_id", columns={"venta_id"})})
 * @ORM\Entity
 */
class EstadoVenta
{
    /**
     * @var int
     *
     * @ORM\Column(name="estado_venta_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $estadoVentaId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="f_creacion", type="datetime", nullable=false)
     */
    private $fCreacion;

    /**
     * @var string
     *
     * @ORM\Column(name="vigente", type="string", length=1, nullable=false)
     */
    private $vigente;

    /**
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estado_id", referencedColumnName="estado_id")
     * })
     */
    private $estado;

    /**
     * @var \Venta
     *
     * @ORM\ManyToOne(targetEntity="Venta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="venta_id", referencedColumnName="venta_id")
     * })
     */
    private $venta;

    public function __construct($venta,$estado)
    {
        $this->venta = $venta;
        $this->estado = $estado;
        $this->fCreacion = new \DateTime();
        $this->vigente = 'S';
    }

    public function getEstadoVentaId(): ?int
    {
        return $this->estadoVentaId;
    }

    public function getFCreacion(): ?\DateTimeInterface
    {
        return $this->fCreacion;
    }

    public function setFCreacion(\DateTimeInterface $fCreacion): self
    {
        $this->fCreacion = $fCreacion;

        return $this;
    }

    public function getVigente(): ?string
    {
        return $this->vigente;
    }

    public function setVigente(string $vigente): self
    {
        $this->vigente = $vigente;

        return $this;
    }

    public function getEstado(): ?Estado
    {
        return $this->estado;
    }

    public function setEstado(?Estado $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getVenta(): ?Venta
    {
        return $this->venta;
    }

    public function setVenta(?Venta $venta): self
    {
        $this->venta = $venta;

        return $this;
    }


}
