<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoPreventa
 *
 * @ORM\Table(name="tipo_preventa")
 * @ORM\Entity
 */
class TipoPreventa
{
    /**
     * @var int
     *
     * @ORM\Column(name="tipo_preventa_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tipoPreventaId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;


    public function getTipoPreventaId(): ?int
    {
        return $this->tipoPreventaId;
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
