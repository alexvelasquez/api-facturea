<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Localidad
 *
 * @ORM\Table(name="localidad", indexes={@ORM\Index(name="provincia_id", columns={"provincia_id"})})
 * @ORM\Entity
 */
class Localidad
{
    /**
     * @var int
     *
     * @ORM\Column(name="localidad_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $localidadId;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;

    /**
     * @var \Provincia
     *
     * @ORM\ManyToOne(targetEntity="Provincia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="provincia_id", referencedColumnName="provincia_id")
     * })
     */
    private $provincia;

    public function __construct($descripcion,$provincia)
    {
        $this->descripcion = $descripcion;
        $this->provincia = $provincia;
    }
    public function getLocalidadId(): ?int
    {
        return $this->localidadId;
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

    public function getProvincia(): ?Provincia
    {
        return $this->provincia;
    }

    public function setProvincia(?Provincia $provincia): self
    {
        $this->provincia = $provincia;

        return $this;
    }


}
