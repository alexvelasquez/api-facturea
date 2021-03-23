<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Venta
 *
 * @ORM\Table(name="venta", indexes={@ORM\Index(name="tipo_venta_id", columns={"tipo_venta_id"}),@ORM\Index(name="cliente_id", columns={"cliente_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\VentaRepository");
 */
class Venta
{
    /**
     * @var int
     *
     * @ORM\Column(name="venta_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $ventaId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="f_venta", type="datetime", nullable=false)
     */
    private $fVenta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="f_creacion", type="datetime", nullable=false)
     */
    private $fCreacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="f_modificacion", type="datetime", nullable=false)
     */
    private $fModificacion;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="f_hasta", type="datetime", nullable=true)
     */
    private $fHasta;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="f_modificacion", type="datetime", nullable=false)
     */

    /**
     * @var \TipoVenta
     *
     * @ORM\ManyToOne(targetEntity="TipoVenta")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_venta_id", referencedColumnName="tipo_venta_id")
     * })
     */
    private $tipoVenta;

    /**
     * @var \Cliente
     *
     * @ORM\ManyToOne(targetEntity="Cliente")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="cliente_id")
     * })
     */
    private $cliente;

    /**
     * One product has many movimientos. This is the inverse side.
     * @ORM\OneToMany(targetEntity="ProductoVenta", mappedBy="venta")
     */
    private $productosVenta;


    public function __construct($cliente,$tipoVenta,$fVenta)
    {
        $this->cliente=$cliente;
        $this->tipoVenta = $tipoVenta;
        $this->fVenta = $fVenta;
        $this->fCreacion = new \DateTime();
        $this->fModificacion = new \DateTime();
    }

    public function getVentaId(): ?int
    {
        return $this->ventaId;
    }
    public function getFVenta(): ?\DateTimeInterface
    {
        return $this->fVenta;
    }

    public function setFVenta(\DateTimeInterface $fVenta): self
    {
        $this->fVenta = $fVenta;
        return $this;
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

    public function getFModificacion(): ?\DateTimeInterface
    {
        return $this->fModificacion;
    }

    public function setFModificacion(\DateTimeInterface $fModificacion): self
    {
        $this->fModificacion = $fModificacion;

        return $this;
    }

    public function getfHasta(): ?\DateTimeInterface
    {
        return $this->fHasta;
    }

    public function setfHasta(\DateTimeInterface $fHasta): self
    {
        $this->fHasta = $fHasta;

        return $this;
    }

    public function getTipoVenta(): ?TipoVenta
    {
        return $this->tipoVenta;
    }

    public function setTipoVenta(?TipoVenta $tipoVenta): self
    {
        $this->tipoVenta = $tipoVenta;

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
