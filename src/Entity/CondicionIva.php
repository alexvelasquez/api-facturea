<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CondicionIva
 *
 * @ORM\Table(name="condicion_iva")
 * @ORM\Entity
 */
class CondicionIva
{
    /**
     * @var int
     *
     * @ORM\Column(name="condicion_iva_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $condicionIvaId;

    /**
     * @var int
     *
     * @ORM\Column(name="afip_id", type="integer", nullable=false)
     */
    private $afipId;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;

    public function __construct($afipId,$descripcion){
        $this->afipId=$afipId;
        $this->descripcion = $descripcion;
      }
    public function getCondicionIvaId(): ?int
    {
        return $this->condicionIvaId;
    }

    public function getAfipId(): ?int
    {
        return $this->afipId;
    }

    public function setAfipId(int $afipId): self
    {
        $this->afipId = $afipId;

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
