<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductoComprobantePreventa
 *
 * @ORM\Table(name="producto_preventa", indexes={@ORM\Index(name="producto_id", columns={"producto_id"}), @ORM\Index(name="preventa_id", columns={"preventa_id"}), @ORM\Index(name="tipo_alicuota_id", columns={"tipo_alicuota_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ProductoPreventaRepository");
 */
class ProductoPreventa
{
    /**
     * @var int
     *
     * @ORM\Column(name="producto_preventa_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productoPreventaId;

    /**
     * @var int
     *
     * @ORM\Column(name="cantidad", type="integer", nullable=false)
     */
    private $cantidad;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal", type="float", precision=10, scale=0, nullable=false)
     */
    private $subtotal;

    /**
     * @var float
     *
     * @ORM\Column(name="subtotal_sin_iva", type="float", precision=10, scale=0, nullable=false)
     */
    private $subtotalSinIva;
    /**
     * @var float
     *
     * @ORM\Column(name="precio_unitario", type="float", precision=10, scale=0, nullable=false)
     */
    private $precioUnitario;

    /**
     * @var float
     *
     * @ORM\Column(name="bonificacion", type="float", precision=10, scale=0, nullable=false)
     */
    private $bonificacion;

    /**
     * @var float
     *
     * @ORM\Column(name="monto_bonif", type="float", precision=10, scale=0, nullable=false)
     */
    private $montoBonif;

    /**
     * @var float
     *
     * @ORM\Column(name="monto_iva", type="float", precision=10, scale=0, nullable=false)
     */
    private $montoIva;

    /**
     * @var \TipoAliCuota
     *
     * @ORM\ManyToOne(targetEntity="TipoAliCuota")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_alicuota_id", referencedColumnName="tipo_alicuota_id",nullable=true)
     * })
     */
    private $alicuota;

    /**
     * @var \Producto
     *
     * @ORM\ManyToOne(targetEntity="Producto")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="producto_id", referencedColumnName="producto_id",nullable=false)
     * })
     */
    private $producto;

    /**
     * @var \Preventa
     *
     * @ORM\ManyToOne(targetEntity="Preventa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="preventa_id", referencedColumnName="preventa_id",nullable=false)
     * })
     */
    private $preventa;

    public function __construct($cantidad, $subtotal, $subtotalSinIva,$bonificacion, $montoBonif, $precioUnitario,$producto, $preventa,$alicuota,$montoIva){
        $this->cantidad = $cantidad;
        $this->subtotal = $subtotal;
        $this->subtotalSinIva = $subtotalSinIva;
        $this->bonificacion = $bonificacion;
        $this->montoBonif = $montoBonif;
        $this->precioUnitario = $precioUnitario;
        $this->preventa = $preventa;
        $this->producto = $producto;
        $this->alicuota = $alicuota;
        $this->montoIva = $montoIva;
    }

    public function getProductoPreventaId(): ?int
    {
        return $this->productoPreventaId;
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

    public function getSubtotal(): ?float
    {
        return $this->subtotal;
    }

    public function setSubtotal(float $subtotal): self
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getSubtotalSinIva(): ?float
    {
        return $this->subtotalSinIva;
    }

    public function setSubtotalSinIva(float $subtotalSinIva): self
    {
        $this->subtotalSinIva = $subtotalSinIva;

        return $this;
    }

    public function getPrecioUnitario(): ?float
    {
        return $this->precioUnitario;
    }

    public function setPrecioUnitario(float $precioUnitario): self
    {
        $this->precioUnitario = $precioUnitario;

        return $this;
    }

    public function getBonificacion(): ?float
    {
        return $this->bonificacion;
    }

    public function setBonificacion(float $bonificacion): self
    {
        $this->bonificacion = $bonificacion;

        return $this;
    }

    public function getMontoBonif(): ?float
    {
        return $this->montoBonif;
    }

    public function setMontoBonif(float $montoBonif): self
    {
        $this->montoBonif = $montoBonif;

        return $this;
    }
    public function getMontoIva(): ?float
    {
        return $this->montoIva;
    }

    public function setMontoIva(float $montoIva): self
    {
        $this->montoIva = $montoIva;

        return $this;
    }
    public function getAliCuota(): ?AliCuota
    {
        return $this->alicuota;
    }

    public function setAliCuota(?AliCuota $alicuota): self
    {
        $this->alicuota = $alicuota;

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


}
