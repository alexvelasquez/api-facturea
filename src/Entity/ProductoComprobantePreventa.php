<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ProductoComprobantePreventa
 *
 * @ORM\Table(name="producto_comprobante_preventa", indexes={@ORM\Index(name="producto_id", columns={"producto_id"}), @ORM\Index(name="comprobante_preventa_id", columns={"comprobante_preventa_id"}), @ORM\Index(name="tipo_alicuota_id", columns={"tipo_alicuota_id"})})
* @ORM\Entity(repositoryClass="App\Repository\ProductoComprobantePreventaRepository");
 */
class ProductoComprobantePreventa
{
    /**
     * @var int
     *
     * @ORM\Column(name="producto_comprobante_preventa_id", type="integer", nullable=false)
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
     * @ORM\Column(name="valor_bonif", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorBonif;

    /**
     * @var float
     *
     * @ORM\Column(name="valor_iva", type="float", precision=10, scale=0, nullable=false)
     */
    private $valorIva;

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
     * @var \ComprobantePreventa
     *
     * @ORM\ManyToOne(targetEntity="ComprobantePreventa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="comprobante_preventa_id", referencedColumnName="comprobante_preventa_id",nullable=false)
     * })
     */
    private $comprobantePreventa;

    public function __construct($cantidad, $subtotal, $bonificacion,  $producto, $comprobantePreventa,$alicuota = null,$valorIva){
        $this->cantidad = $cantidad;
        $this->subtotal = $subtotal;
        $this->comprobantePreventa = $comprobantePreventa;
        $this->producto = $producto;
        $this->alicuota = $alicuota;
        $this->valorBonif = $bonificacion;
        $this->valorIva = $valorIva;
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

    public function getValorBonif(): ?float
    {
        return $this->valorBonif;
    }

    public function setValorBonif(float $valorBonif): self
    {
        $this->valorBonif = $valorBonif;

        return $this;
    }

    public function getValorIva(): ?float
    {
        return $this->valorIva;
    }

    public function setValorIva(float $valorIva): self
    {
        $this->valorIva = $valorIva;

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

    public function getComprobantePreventa(): ?ComprobantePreventa
    {
        return $this->comprobantePreventa;
    }

    public function setComprobantePreventa(?ComprobantePreventa $comprobantePreventa): self
    {
        $this->comprobantePreventa = $comprobantePreventa;
        return $this;
    }


}
