<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoVenta
 *
 * @ORM\Table(name="tipo_venta")
 * @ORM\Entity
 */
class TipoVenta
{
    /**
     * @var int
     *
     * @ORM\Column(name="tipo_venta_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tipoVentaId;

    /**
     * @var string
     *
     * @ORM\Column(name="codigo", type="string", length=128, nullable=false)
     */
    private $codigo;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=128, nullable=false)
     */
    private $descripcion;

    public function getTipoVentaId(): ?int
    {
        return $this->tipoVentaId;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
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


}
