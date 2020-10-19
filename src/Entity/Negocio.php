<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Negocio
 *
 * @ORM\Table(name="negocio",indexes={@ORM\Index(name="localidad_id", columns={"localidad_id"}), @ORM\Index(name="condicion_iva_id", columns={"condicion_iva_id"})})
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
     * @ORM\Column(name="direccion", type="string", length=255, nullable=true)
     */
    private $direccion;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var int|null
     *
     * @ORM\Column(name="telefono", type="integer", nullable=true)
     */
    private $telefono;

    /**
     * @var int|null
     *
     * @ORM\Column(name="cuit_cuil", type="integer", nullable=true)
     */
    private $cuitCuil;

    /**
     * @var int|null
     *
     * @ORM\Column(name="iibb", type="integer", nullable=true)
     */
    private $iibb;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="inicio_actividad", type="date", nullable=true)
     */
    private $inicioActividad;

    /**
     * @var int|null
     *
     * @ORM\Column(name="codigo_postal", type="string", length=255, nullable=true)
     */
    private $codigoPostal;

    /**
     * @var int|null
     *
     * @ORM\Column(name="logo", type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @var int|null
     *
     * @ORM\Column(name="punto_vta", type="integer", length=5, nullable=true)
     */
    private $puntoVta;

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
     * @var \CondicionIva
     *
     * @ORM\ManyToOne(targetEntity="CondicionIva")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="condicion_iva_id", referencedColumnName="condicion_iva_id")
     * })
     */
    private $condicionIva;

    public function __construct(){
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

    public function getDireccion(): ?string
    {
        return $this->direccion;
    }

    public function setDireccion(?string $direccion): self
    {
        $this->direccion = $direccion;

        return $this;
    }

    public function getTelefono(): ?int
    {
        return $this->telefono;
    }

    public function setTelefono(?int $telefono): self
    {
        $this->telefono = $telefono;

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

    public function getCodigoPostal(): ?string
    {
        return $this->codigoPostal;
    }

    public function setCodigoPostal(string $codigoPostal): self
    {
        $this->codigoPostal = $codigoPostal;

        return $this;
    }

    public function getLogo()
    {
        return $this->logo;
    }

    public function setLogo($logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getCuitCuil(): ?int
    {
        return $this->cuitCuil;
    }

    public function setCuitCuil(?int $cuitCuil): self
    {
        $this->cuitCuil = $cuitCuil;

        return $this;
    }


    public function getIibb(): ?int
    {
        return $this->iibb;
    }

    public function setIibb(?int $iibb): self
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

    public function getPuntoVta(): ?int
    {
        return $this->puntoVta;
    }

    public function setPuntoVta(?int $puntoVta): self
    {
        $this->puntoVta = $puntoVta;

        return $this;
    }

    public function getPtoVtaFactura()
    {
        return str_pad($this->puntoVta, 5, "0", STR_PAD_LEFT);
    }
    public function getCondicionIva(): ?CondicionIva
    {
        return $this->condicionIva;
    }

    public function setCondicionIva(?CondicionIva $condIva): self
    {
        $this->condicionIva = $condIva;

        return $this;
    }

    public function getLocalidad(): ?Localidad
    {
        return $this->localidad;
    }

    public function setLocalidad(Localidad $localidad): self
    {
        $this->localidad = $localidad;

        return $this;
    }
}
