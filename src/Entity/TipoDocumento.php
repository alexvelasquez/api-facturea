<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoDocumento
 *
 * @ORM\Table(name="tipo_documento")
 * @ORM\Entity
 */
class TipoDocumento
{
    /**
     * @var int
     *
     * @ORM\Column(name="tipo_documento_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tipoDocumentoId;

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

    public function getTipoDocumentoId(): ?int
    {
        return $this->tipoDocumentoId;
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
