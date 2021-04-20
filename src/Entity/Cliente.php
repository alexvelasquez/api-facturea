<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cliente
 *
 * @ORM\Table(name="cliente", indexes={@ORM\Index(name="negocio_id", columns={"negocio_id"}), @ORM\Index(name="tipo_documento_id", columns={"tipo_documento_id"}), @ORM\Index(name="condicion_iva_id", columns={"condicion_iva_id"}), @ORM\Index(name="localidad_id", columns={"localidad_id"})})
 * @ORM\Entity
 */
class Cliente
{
    /**
     * @var int
     *
     * @ORM\Column(name="cliente_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $clienteId;

    /**
     * @var string
     *
     * @ORM\Column(name="razon_social", type="string", length=255, nullable=false)
     */
    private $razonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @var string
     *
     * @ORM\Column(name="documento", type="string", length=255, nullable=true)
     */
    private $documento;


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
     * @var \CondicionIva
     *
     * @ORM\ManyToOne(targetEntity="CondicionIva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="condicion_iva_id", referencedColumnName="condicion_iva_id")
     * })
     */
    private $condicionIva;

    /**
     * @var \Localidad
     *
     * @ORM\ManyToOne(targetEntity="Localidad")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="localidad_id", referencedColumnName="localidad_id")
     * })
     */
    private $localidad;

    /**
     * @var \Negocio
     *
     * @ORM\ManyToOne(targetEntity="Negocio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="negocio_id", referencedColumnName="negocio_id")
     * })
     */
    private $negocio;


    /**
     * @var \TipoDocumento
     *
     * @ORM\ManyToOne(targetEntity="TipoDocumento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_documento_id", referencedColumnName="tipo_documento_id")
     * })
     */
    private $tipoDocumento;

    /**
     * One Customer has One Cart.
     * @ORM\OneToOne(targetEntity="CuentaCorriente", mappedBy="cliente")
     */
    private $cuentaCorriente;

    public function __construct($razonSocial, $email, $localidad, $direccion, $telefono,$tipoDoc, $documento,$condIva, $negocio ){
        $this->razonSocial = $razonSocial;
        $this->email = $email;
        $this->localidad = $localidad;
        $this->direccion = $direccion;
        $this->tipoDocumento = $tipoDoc;
        $this->documento = $documento;
        $this->condicionIva = $condIva;
        $this->negocio = $negocio;
        $this->telefono = $telefono;
        $this->fCreacion = new \DateTime();
        $this->fModificacion = new \DateTime();
    }
    public function getClienteId(): ?int
    {
        return $this->clienteId;
    }

    public function getRazonSocial(): ?string
    {
        return $this->razonSocial;
    }

    public function setRazonSocial(string $razonSocial): self
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getDocumento(): ?string
    {
        return $this->documento;
    }

    public function setDocumento(string $documento): self
    {
        $this->documento = $documento;

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

    public function getFHasta(): ?\DateTimeInterface
    {
        return $this->fHasta;
    }

    public function setFHasta(\DateTimeInterface $fHasta): self
    {
        $this->fHasta = $fHasta;

        return $this;
    }

    public function getCondicionIva(): ?CondicionIva
    {
        return $this->condicionIva;
    }

    public function setCondicionIva(?CondicionIva $condicionIva): self
    {
        $this->condicionIva = $condicionIva;

        return $this;
    }

    public function getLocalidad(): ?Localidad
    {
        return $this->localidad;
    }

    public function setLocalidad(?Localidad $localidad): self
    {
        $this->localidad = $localidad;

        return $this;
    }

    public function getNegocio(): ?Negocio
    {
        return $this->negocio;
    }

    public function setNegocio(?Negocio $negocio): self
    {
        $this->negocio = $negocio;

        return $this;
    }

    public function getTipoDocumento(): ?TipoDocumento
    {
        return $this->tipoDocumento;
    }

    public function setTipoDocumento(?TipoDocumento $tipoDocumento): self
    {
        $this->tipoDocumento = $tipoDocumento;

        return $this;
    }
    public function getCuentaCorriente(): ?CuentaCorriente
    {
        return $this->cuentaCorriente;
    }

}
