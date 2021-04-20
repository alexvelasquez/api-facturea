<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Negocio
 *
 * @ORM\Table(name="negocio", indexes={@ORM\Index(name="condicion_iva_id", columns={"condicion_iva_id"}), @ORM\Index(name="localidad_id", columns={"localidad_id"})})
 * @ORM\Entity
 */
class Negocio
{
    /**
     * @var int
     *
     * @ORM\Column(name="negocio_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $negocioId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="razon_social", type="string", length=255, nullable=true)
     */
    private $razonSocial;

    /**
     * @var string|null
     *
     * @ORM\Column(name="nombre_fantasia", type="string", length=255, nullable=true)
     */
    private $nombreFantasia;

    /**
     * @var string|null
     *
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string|null
     *
     * @ORM\Column(name="telefono", type="string", length=255, nullable=true)
     */
    private $telefono;

    /**
     * @var string|null
     *
     * @ORM\Column(name="cuit_cuil", type="string", length=255, nullable=true)
     */
    private $cuitCuil;

    /**
     * @var string|null
     *
     * @ORM\Column(name="iibb", type="string", length=255, nullable=true)
     */
    private $iibb;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="inicio_actividad", type="date", nullable=true)
     */
    private $inicioActividad;

    /**
     * @var string|null
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="punto_vta", type="integer", nullable=true)
     */
    private $puntoVta;

    /**
     * @var string
     *
     * @ORM\Column(name="factura_electronica", type="string", length=1, nullable=true)
     */
    private $facturaElectronica;

    /**
     * @var string
     *
     * @ORM\Column(name="pedido", type="string", length=1, nullable=true)
     */
    private $pedido;

    /**
     * @var string
     *
     * @ORM\Column(name="configuracion", type="string", length=1, nullable=true)
     */
    private $configuracion;

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

    
    public function __construct(){
        $this->facturaElectronica = 'N';
        $this->pedido = 'N';
        $this->configuracion = 'N';
      }
    public function getNegocioId(): ?int
    {
        return $this->negocioId;
    }

    public function getRazonSocial(): ?string
    {
        return $this->razonSocial;
    }

    public function setRazonSocial(?string $razonSocial): self
    {
        $this->razonSocial = $razonSocial;

        return $this;
    }

    public function getNombreFantasia(): ?string
    {
        return $this->nombreFantasia;
    }

    public function setNombreFantasia(?string $nombreFantasia): self
    {
        $this->nombreFantasia = $nombreFantasia;

        return $this;
    }

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelefono(): ?string
    {
        return $this->telefono;
    }

    public function setTelefono(?string $telefono): self
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getCuitCuil(): ?string
    {
        return $this->cuitCuil;
    }

    public function setCuitCuil(?string $cuitCuil): self
    {
        $this->cuitCuil = $cuitCuil;

        return $this;
    }

    public function getIibb(): ?string
    {
        return $this->iibb;
    }

    public function setIibb(?string $iibb): self
    {
        $this->iibb = $iibb;

        return $this;
    }

    public function getInicioActividad(): ?\DateTimeInterface
    {
        return $this->inicioActividad;
    }

    public function setInicioActividad(?\DateTimeInterface $inicioActividad): self
    {
        $this->inicioActividad = $inicioActividad;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getPuntoVta(): ?int
    {
        return $this->puntoVta;
    }

    public function setPuntoVta(?int $puntoVta): self
    {
        $this->puntoVta = $puntoVta;

        return $this;
    }

    public function getFacturaElectronica(): ?string
    {
        return $this->facturaElectronica;
    }
    
    public function getPtoVtaFactura()
    {
        return str_pad($this->puntoVta, 5, "0", STR_PAD_LEFT);
    }

    public function setFacturaElectronica(string $facturaElectronica): self
    {
        $this->facturaElectronica = $facturaElectronica;

        return $this;
    }

    public function getPedido(): ?string
    {
        return $this->pedido;
    }

    public function setPedido(string $pedido): self
    {
        $this->pedido = $pedido;

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

    public function getConfiguracion()
    {
        return $this->configuracion;
    }

    public function setConfiguracion($configuracion): self
    {
        $this->configuracion = $configuracion;
        return $this;
    }


}
