<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EstadoPago
 *
 * @ORM\Table(name="estado_pago")
 * @ORM\Entity
 */
class EstadoPago
{
    /**
     * @var int
     *
     * @ORM\Column(name="estado_pago_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $estadoPagoId;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;

    public function getEstadoPagoId(): ?int
    {
        return $this->estadoPagoId;
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
