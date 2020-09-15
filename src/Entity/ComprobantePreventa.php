<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ComprobantePreventa
 *
 * @ORM\Table(name="comprobante_preventa", indexes={@ORM\Index(name="estado_pago_id", columns={"estado_pago_id"}), @ORM\Index(name="tipo_comprobante_id", columns={"tipo_comprobante_id"}),@ORM\Index(name="cliente_id", columns={"cliente_id"})})
 * @ORM\Entity
 */
class ComprobantePreventa
{
    /**
     * @var int
     *
     * @ORM\Column(name="comprobante_preventa_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $comprobantePreventaId;

    /**
     * @var int
     *
     * @ORM\Column(name="monto", type="integer", nullable=false)
     */
    private $monto;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="date", nullable=false)
     */
    private $fecha;

    /**
     * @var string
     *
     * @ORM\Column(name="vigente", type="string", length=1, nullable=false)
     */
    private $vigente;

    /**
     * @var \EstadoPago
     *
     * @ORM\ManyToOne(targetEntity="EstadoPago")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estado_pago_id", referencedColumnName="estado_pago_id",nullable=true)
     * })
     */
    private $estadoPago;

    /**
     * @var \TipoComprobante
     *
     * @ORM\ManyToOne(targetEntity="TipoComprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_comprobante_id", referencedColumnName="tipo_comprobante_id",nullable=true)
     * })
     */
    private $tipoComprobante;

    /**
     * @var \Cliente
     *
     * @ORM\ManyToOne(targetEntity="Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="cliente_id",nullable=false)
     * })
     */
    private $cliente;

    public function __construct($cliente,$montoTotal,$fechaComprobante,$vigente){
      $this->cliente = $cliente;
      $this->monto = $montomontoTotal;
      $this->fecha = $fechaCfechaComprobante;
      $this->vigente = $vigente;
    }

    public function getComprobantePreventaId(): ?int
    {
        return $this->comprobantePreventaId;
    }

    public function getMonto(): ?int
    {
        return $this->monto;
    }

    public function setMonto(int $monto): self
    {
        $this->monto = $monto;

        return $this;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

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

    public function getEstadoPago(): ?EstadoPago
    {
        return $this->estadoPago;
    }

    public function setEstadoPago(?EstadoPago $estadoPago): self
    {
        $this->estadoPago = $estadoPago;

        return $this;
    }

    public function getTipoComprobante(): ?TipoComprobante
    {
        return $this->tipoComprobante;
    }

    public function setTipoComprobante(?TipoComprobante $tipoComprobante): self
    {
        $this->tipoComprobante = $tipoComprobante;

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


}
