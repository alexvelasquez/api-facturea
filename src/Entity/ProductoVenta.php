<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductoVenta
 *
 * @ORM\Table(name="producto_venta", indexes={@ORM\Index(name="venta_id", columns={"venta_id"}), @ORM\Index(name="producto_id", columns={"producto_id"}), @ORM\Index(name="tipo_alicuota_id", columns={"tipo_alicuota_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProductoVentaRepository");
 */
class ProductoVenta
{
    /**
     * @var int
     *
     * @ORM\Column(name="producto_venta_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productoVentaId;

    /**
     * @var int
     *
     * @ORM\Column(name="cantidad", type="integer", nullable=false)
     */
    private $cantidad;

    /**
     * @var int
     *
     * @ORM\Column(name="subtotal", type="integer", nullable=false)
     */
    private $subtotal;

    /**
     * @var int
     *
     * @ORM\Column(name="bonificacion", type="integer", nullable=false)
     */
    private $bonificacion;

    /**
     * @var int
     *
     * @ORM\Column(name="subtotal_sin_iva", type="integer", nullable=false)
     */
    private $subtotalSinIva;

    /**
     * @var int
     *
     * @ORM\Column(name="precio_unitario", type="integer", nullable=false)
     */
    private $precioUnitario;

    /**
     * @var int
     *
     * @ORM\Column(name="monto_bonif", type="integer", nullable=false)
     */
    private $montoBonif;

    /**
     * @var int
     *
     * @ORM\Column(name="monto_iva", type="integer", nullable=false)
     */
    private $montoIva;

    /**
     * @var \Producto
     *
     * @ORM\ManyToOne(targetEntity="Producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto_id", referencedColumnName="producto_id")
     * })
     */
    private $producto;

    /**
     * @var \TipoAlicuota
     *
     * @ORM\ManyToOne(targetEntity="TipoAlicuota")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_alicuota_id", referencedColumnName="tipo_alicuota_id", nullable=true)
     * })
     */
    private $tipoAlicuota;

    /**
     * @var \Venta
     *
     * @ORM\ManyToOne(targetEntity="Venta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="venta_id", referencedColumnName="venta_id")
     * })
     */
    private $venta;

    public function __construct($cantidad,$subtotal,$subtotalSinIva,$bonificacion,$montoBonif,$precioUnitario,$producto,$venta,$alicuota,$montoIva)
    {
        $this->cantidad = $cantidad;
        $this->subtotal = $subtotal;
        $this->subtotalSinIva = $subtotalSinIva;
        $this->bonificacion = $bonificacion;
        $this->montoBonif = $montoBonif;
        $this->precioUnitario = $precioUnitario;
        $this->producto = $producto;
        $this->venta = $venta;
        $this->tipoAlicuota = $alicuota;
        $this->montoIva = $montoIva;
    }

    public function getProductoVentaId(): ?int
    {
        return $this->productoVentaId;
    }

    public function getCantidad(): ?int
    {
        return $this->cantidad;
    }

    public function setCantidad(int $cantidad): self
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getSubtotal(): ?int
    {
        return $this->subtotal;
    }

    public function setSubtotal(int $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getBonificacion(): ?int
    {
        return $this->bonificacion;
    }

    public function setBonificacion(int $bonificacion): self
    {
        $this->bonificacion = $bonificacion;

        return $this;
    }

    public function getSubtotalSinIva(): ?int
    {
        return $this->subtotalSinIva;
    }

    public function setSubtotalSinIva(int $subtotalSinIva): self
    {
        $this->subtotalSinIva = $subtotalSinIva;

        return $this;
    }

    public function getPrecioUnitario(): ?int
    {
        return $this->precioUnitario;
    }

    public function setPrecioUnitario(int $precioUnitario): self
    {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    public function getMontoBonif(): ?int
    {
        return $this->montoBonif;
    }

    public function setMontoBonif(int $montoBonif): self
    {
        $this->montoBonif = $montoBonif;

        return $this;
    }

    public function getMontoIva(): ?int
    {
        return $this->montoIva;
    }

    public function setMontoIva(int $montoIva): self
    {
        $this->montoIva = $montoIva;

        return $this;
    }

    public function getProducto(): ?Producto
    {
        return $this->producto;
    }

    public function setProducto(?Producto $producto): self
    {
        $this->producto = $producto;

        return $this;
    }

    public function getTipoAlicuota(): ?TipoAlicuota
    {
        return $this->tipoAlicuota;
    }

    public function setTipoAlicuota(?TipoAlicuota $tipoAlicuota): self
    {
        $this->tipoAlicuota = $tipoAlicuota;

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
