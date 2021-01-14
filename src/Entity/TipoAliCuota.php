<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TipoAlicuota
 *
 * @ORM\Table(name="tipo_alicuota")
 * @ORM\Entity
 */
class TipoAlicuota
{
    /**
     * @var int
     *
     * @ORM\Column(name="tipo_alicuota_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tipoAlicuotaId;

    /**
     * @var int
     *
     * @ORM\Column(name="afip_id", type="integer", nullable=false)
     */
    private $afipId;

    /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=128, nullable=false)
     */
    private $descripcion;

    public function __construct($afipId,$descripcion){
        $this->afipId=$afipId;
        $this->descripcion = $descripcion;
      }
    public function getTipoAlicuotaId(): ?int
    {
        return $this->tipoAlicuotaId;
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

    public function getValor(): ?float
    {
        return $this->valor;
    }

    public function setValor(float $valor): self
    {
        $this->valor = $valor;

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
