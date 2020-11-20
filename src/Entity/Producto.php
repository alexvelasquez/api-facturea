<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Producto
 *
 * @ORM\Table(name="producto", indexes={@ORM\Index(name="marca_id", columns={"marca_id"}),@ORM\Index(name="categoria_id", columns={"categoria_id"})})
 * @ORM\Entity
 */
class Producto
{
    /**
     * @var int
     *
     * @ORM\Column(name="producto_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $productoId;

    /**
     * @var int
     *
     * @ORM\Column(name="codigo", type="integer", nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;

    /**
     * @var int
     *
     * @ORM\Column(name="stock", type="integer", nullable=false)
     */
    private $stock;

    /**
     * @var float
     *
     * @ORM\Column(name="precio_compra", type="float", precision=10, scale=0, nullable=false)
     */
    private $precioCompra;

    /**
     * @var int
     *
     * @ORM\Column(name="aumento", type="integer", nullable=false)
     */
    private $aumento;


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
     * @var \DateTime|null
     *
     * @ORM\Column(name="f_hasta", type="datetimetz", nullable=true)
     */
    private $fHasta;

    /**
     * @var \Marca
     *
     * @ORM\ManyToOne(targetEntity="Marca")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="marca_id", referencedColumnName="marca_id" ,nullable=true, onDelete="SET NULL")
     * })
     */
    private $marca;

    /**
     * @var \Categoria
     *
     * @ORM\ManyToOne(targetEntity="Categoria")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="categoria_id", referencedColumnName="categoria_id", nullable=true, onDelete="SET NULL")
     * })
     */
    private $categoria;

    /**
     * @var \Negocio
     *
     * @ORM\ManyToOne(targetEntity="Negocio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="negocio_id", referencedColumnName="negocio_id", nullable=false)
     * })
     */
    private $negocio;


    public function __construct($descripcion, $codigo, $stock, $categoria,$marca, $precioCompra, $aumento,$negocio ){
        $this->descripcion = $descripcion;
        $this->codigo = $codigo;
        $this->stock = $stock;
        $this->categoria = $categoria;
        $this->marca = $marca;
        $this->precioCompra = $precioCompra;
        $this->aumento = $aumento;
        $this->negocio = $negocio;

        $this->fCreacion = new \DateTime();
        $this->fModificacion = new \DateTime();
    }

    public function getProductoId(): ?int
    {
        return $this->productoId;
    }

    public function getCodigo(): ?int
    {
        return $this->codigo;
    }

    public function setCodigo(int $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }


    public function getPrecioCompra(): ?float
    {
        return $this->precioCompra;
    }

    public function setPrecioCompra(float $precioCompra): self
    {
        $this->precioCompra = $precioCompra;

        return $this;
    }

    public function getAumento(): ?int
    {
        return $this->aumento;
    }

    public function setAumento(int $aumento): self
    {
        $this->aumento = $aumento;

        return $this;
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

    public function getFHasta(): ?\DateTimeInterface
    {
        return $this->fHasta;
    }

    public function setFHasta(?\DateTimeInterface $fHasta): self
    {
        $this->fHasta = $fHasta;

        return $this;
    }

    public function getMarca(): ?Marca
    {
        return $this->marca;
    }

    public function setMarca(?Marca $marca): self
    {
        $this->marca = $marca;

        return $this;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }
    public function getNegocio(): ?Negocio
    {
        return $this->negocio;
    }

    public function getPrecioPublicado(): ?string
    {
        return number_format($this->precioCompra + ($this->precioCompra * ($this->aumento/100)), 2, '.', ',');
    }



}
