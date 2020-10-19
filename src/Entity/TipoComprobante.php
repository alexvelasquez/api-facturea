<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CondicionIva
 *
 * @ORM\Table(name="tipo_comprobante")
 * @ORM\Entity(repositoryClass="App\Repository\TipoComprobanteRepository");
 */
class TipoComprobante
{
    /**
     * @var int
     *
     * @ORM\Column(name="tipo_comprobante_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tipoComprobanteId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="afip_id", type="integer", nullable=false)
     */
    private $afipId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=1, nullable=false)
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;


    public function getTipoCompobanteId(): ?int
    {
        return $this->tipoComprobanteId;
    }



    public function getAfipId(): ?string
    {
        return $this->afipId;
    }

    public function getAfipFactura()
    {
        return str_pad($this->afipId, 3, "0", STR_PAD_LEFT);
    }
    public function setAfipId(?string $afipId): self
    {
        $this->afipId = $afipId;

        return $this;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }


    public function setCodigo(?string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }
    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(?string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

}
