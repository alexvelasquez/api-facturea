<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ComprobantePreventa
 *
 * @ORM\Table(name="comprobante_preventa", indexes={@ORM\Index(name="estado_id", columns={"estado_id"}), @ORM\Index(name="tipo_comprobante_id", columns={"tipo_comprobante_id"}),@ORM\Index(name="preventa_id", columns={"preventa_id"}),@ORM\Index(name="condicion_venta_id", columns={"condicion_venta_id"})})
 * @ORM\Entity(repositoryClass="App\Repository\ComprobantePreventaRepository");
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
     * @ORM\Column(name="numero", type="integer", nullable=true)
     */
    private $numero;

    /**
     * @var int
     *
     * @ORM\Column(name="punto_venta", type="integer", nullable=true)
     */
    private $puntoVenta;

    /**
     * @var string|null
     *
     * @ORM\Column(name="vigente", type="string", length=1, nullable=false)
     */
    private $vigente;

    /**
     * @var \Estado
     *
     * @ORM\ManyToOne(targetEntity="Estado", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="estado_id", referencedColumnName="estado_id",nullable=false)
     * })
     */
    private $estado;

    /**
     * @var \TipoComprobante
     *
     * @ORM\ManyToOne(targetEntity="TipoComprobante", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_comprobante_id", referencedColumnName="tipo_comprobante_id",nullable=true)
     * })
     */
    private $tipoComprobante;

    /**
     * @var \CondicionVenta
     *
     * @ORM\ManyToOne(targetEntity="CondicionVenta", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="condicion_venta_id", referencedColumnName="condicion_venta_id",nullable=true)
     * })
     */
    private $condicionVenta;

    /**
     * @var \Preventa
     *
     * @ORM\ManyToOne(targetEntity="Preventa", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="preventa_id", referencedColumnName="preventa_id",nullable=false)
     * })
     */
    private $preventa;

    public function __construct($preventa,$estado,$tipoComprobante = null,$condicionVta=null,$numero=null,$puntoVenta=null){
      $this->preventa = $preventa;
      $this->estado = $estado;
      $this->vigente = 'S';
      $this->tipoComprobante = $tipoComprobante;
      $this->condicionVenta = $condicionVta;
      $this->numero = $numero;
      $this->puntoVenta = $puntoVenta;

    }

    public function getComprobantePreventaId(): ?int
    {
        return $this->comprobantePreventaId;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function getNumeroFactura()
    {
        return str_pad($this->numero, 8, "0", STR_PAD_LEFT);
    }

    public function getPuntoVenta(): ?int
    {
        return $this->puntoVenta;
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

    public function getTipoComprobante(): ?TipoComprobante
    {
        return $this->tipoComprobante;
    }

    public function setTipoComprobante(?TipoComprobante $tipoComprobante): self
    {
        $this->tipoComprobante = $tipoComprobante;

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

    public function getPreventa(): ?Preventa
    {
        return $this->preventa;
    }

    public function setPreventa(?Preventa $preventa): self
    {
        $this->preventa = $preventa;

        return $this;
    }


}
