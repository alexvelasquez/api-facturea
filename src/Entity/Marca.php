<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Marca
 *
 * @ORM\Table(name="marca", indexes={@ORM\Index(name="negocio_id", columns={"negocio_id"})})
 * @ORM\Entity
 */
class Marca
{
    /**
     * @var int
     *
     * @ORM\Column(name="marca_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $marcaId;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255, nullable=false)
     */
    private $descripcion;

    /**
     * @var \Negocio
     *
     * @ORM\ManyToOne(targetEntity="Negocio")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="negocio_id", referencedColumnName="negocio_id", nullable=false)
     * })
     */
    private $negocio;





    public function __construct($descripcion,$negocio)
    {
        $this->descripcion = $descripcion;
        $this->negocio = $negocio;
    }

    public function getMarcaId(): ?int
    {
        return $this->marcaId;
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
    public function getNegocio(): ?Negocio
    {
        return $this->negocio;
    }

    public function setNegocio(?Negocio $negocio): self
    {
        $this->negocio = $negocio;

        return $this;
    }


}
