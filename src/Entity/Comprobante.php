<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comprobante
 *
 * @ORM\Table(name="comprobante", indexes={@ORM\Index(name="venta_id", columns={"venta_id"}), @ORM\Index(name="condicion_venta_id", columns={"condicion_venta_id"}), @ORM\Index(name="tipo_comprobante_id", columns={"tipo_comprobante_id"})})
 * @ORM\Entity
 */
class Comprobante
{
    /**
     * @var int
     *
     * @ORM\Column(name="comprobante_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $comprobanteId;

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer", nullable=false)
     */
    private $numero;

    /**
     * @var int
     *
     * @ORM\Column(name="punto_venta", type="integer", nullable=false)
     */
    private $puntoVenta;

    /**
     * @var string
     *
     * @ORM\Column(name="remito", type="string", length=255, nullable=true)
     */
    private $remito;

    /**
     * @var \CondicionVenta
     *
     * @ORM\ManyToOne(targetEntity="CondicionVenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="condicion_venta_id", referencedColumnName="condicion_venta_id")
     * })
     */
    private $condicionVenta;

    /**
     * @var \TipoComprobante
     *
     * @ORM\ManyToOne(targetEntity="TipoComprobante")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_comprobante_id", referencedColumnName="tipo_comprobante_id")
     * })
     */
    private $tipoComprobante;

    /**
     * @var \Venta
     *
     * @ORM\ManyToOne(targetEntity="Venta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="venta_id", referencedColumnName="venta_id")
     * })
     */
    private $venta;

    public function __construct($condicionVenta,$venta,$tipoComprobante,$numero,$ptoVenta,$remito)
    {   
        $this->condicionVenta = $condicionVenta;
        $this->venta = $venta;
        $this->tipoComprobante = $tipoComprobante;
        $this->numero = $numero;
        $this->puntoVenta=$ptoVenta;
        $this->remito = $remito;   
    }

    public function getComprobanteId(): ?int
    {
        return $this->comprobanteId;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getPuntoVenta(): ?int
    {
        return $this->puntoVenta;
    }

    public function setPuntoVenta(int $puntoVenta): self
    {
        $this->puntoVenta = $puntoVenta;

        return $this;
    }

    public function getRemito(): ?string
    {
        return $this->remito;
    }

    public function setRemito(string $remito): self
    {
        $this->remito = $remito;

        return $this;
    }

    public function getCondicionVenta(): ?CondicionVenta
    {
        return $this->condicionVenta;
    }

    public function setCondicionVenta(?CondicionVenta $condicionVenta): self
    {
        $this->condicionVenta = $condicionVenta;

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
