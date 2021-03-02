<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoMovimiento
 *
 * @ORM\Table(name="tipo_movimiento")
 * @ORM\Entity
 */
class TipoMovimiento
{
    /**
     * @var int
     *
     * @ORM\Column(name="tipo_movimiento_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tipo_movimiento_id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="codigo", type="string", length=64, nullable=false)
     */
    private $codigo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;


    public function getTipoConceptoId(): ?int
    {
        return $this->condicionIvaId;
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
