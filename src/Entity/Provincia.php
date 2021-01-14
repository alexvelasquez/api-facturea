<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Provincia
 *
 * @ORM\Table(name="provincia")
 * @ORM\Entity
 */
class Provincia
{
    /**
     * @var int
     *
     * @ORM\Column(name="provincia_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $provinciaId;

    /**
     * @var int
     *
     * @ORM\Column(name="geo_id", type="integer", nullable=false)
     */
    private $geoId;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;
        public function __construct($geoId,$descripcion){
        $this->geoId=$geoId;
        $this->descripcion = $descripcion;
      }

    public function getProvinciaId(): ?int
    {
        return $this->provinciaId;
    }

    public function getGeoId(): ?int
    {
        return $this->geoId;
    }

    public function setGeoId(int $geoId): self
    {
        $this->geoId = $geoId;

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
