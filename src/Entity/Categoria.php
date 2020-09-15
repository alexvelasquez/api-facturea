<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Categoria
 *
 * @ORM\Table(name="categoria",indexes={@ORM\Index(name="negocio_id", columns={"negocio_id"})})
 * @ORM\Entity
 */
class Categoria
{
    /**
     * @var int
     *
     * @ORM\Column(name="categoria_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $categoriaId;

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


    public function __construct($descripcion,$negocio){
        $this->descripcion = $descripcion;
        $this->negocio = $negocio;
    }
    
    public function getCategoriaId(): ?int
    {
        return $this->categoriaId;
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
