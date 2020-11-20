<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * CondicionIva
 *
 * @ORM\Table(name="tipo_concepto")
 * @ORM\Entity
 */
class TipoConcepto
{
    /**
     * @var int
     *
     * @ORM\Column(name="tipo_concepto_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tipoConceptoId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="afip_id", type="integer", nullable=false)
     */
    private $afipId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;



    public function __construct($afipId,$descripcion){
      $this->afipId=$afipId;
      $this->descripcion = $descripcion;
    }

    public function getTipoConceptoId(): ?int
    {
        return $this->condicionIvaId;
    }

    public function getAfipId(): ?string
    {
        return $this->afipId;
    }

    public function setAfipId(?string $afipId): self
    {
        $this->afipId = $afipId;

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
