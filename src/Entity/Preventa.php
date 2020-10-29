<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoPago
 *
 * @ORM\Table(name="preventa", indexes={@ORM\Index(name="cliente_id", columns={"cliente_id"}), @ORM\Index(name="tipo_preventa_id", columns={"tipo_preventa_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\PreventaRepository");
 */
class Preventa
{
    /**
     * @var int
     *
     * @ORM\Column(name="preventa_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $preventaId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fecha", type="datetimetz", nullable=false)
     */
    private $fecha;

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
     * @var \Cliente
     *
     * @ORM\ManyToOne(targetEntity="Cliente",fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cliente_id", referencedColumnName="cliente_id",nullable=false)
     * })
     */
    private $cliente;

    /**
     * @var \TipoPreventa
     *
     * @ORM\ManyToOne(targetEntity="TipoPreventa")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_preventa_id", referencedColumnName="tipo_preventa_id",nullable=true)
     * })
     */
    private $tipoPreventa;

    public function __construct(?Cliente  $cliente, ?TipoPreventa $tipoPreventa,$fecha)
    {
        $this->cliente = $cliente;
        $this->tipoPreventa = $tipoPreventa;
        $this->fecha = new \DateTime($fecha);
        $this->fCreacion = new \DateTime();
        $this->fModificacion = new \DateTime();
    }

    public function getPreventaId(): ?int
    {
        return $this->preventaId;
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

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function setCliente(?Cliente $cliente): self
    {
        $this->cliente = $cliente;
        return $this;
    }
    public function getTipoPreventa(): ?TipoPreventa
    {
        return $this->tipoPreventa;
    }

    public function setTipoPreventa(?TipoPreventa $tipoPreventa): self
    {
        $this->tipoPreventa = $tipoPreventa;
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


}
