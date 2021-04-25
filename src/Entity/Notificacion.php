<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notificacion
 *
 * @ORM\Table(name="notificacion", indexes={@ORM\Index(name="usuario_id", columns={"usuario_id"})})
 * @ORM\Entity
 */
class Notificacion
{
    /**
     * @var int
     *
     * @ORM\Column(name="notificacion_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $notificacionId;

    /**
     * @var int
     *
     * @ORM\Column(name="mensaje", type="text", nullable=false)
     */
    private $mensaje;

    /**
     * @var int
     *
     * @ORM\Column(name="leido", type="string", length=1, nullable=false)
     */
    private $leido;

    /**
     * @var int
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=false)
     */
    private $titulo;

    /**
     * @var int
     *
     * @ORM\Column(name="redireccion",type="string", length=255, nullable=true)
     */
    private $redireccion;

    /**
     * @var \Usuario
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_id", referencedColumnName="id")
     * })
     */
    private $usuario;

    public function __construct($titulo,$mensaje,$user,$url)
    {
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
        $this->usuario = $user;
        $this->redireccion = $url;
        $this->leido = 'N';
    }

    public function getNotificacionId(): ?int
    {
        return $this->notificacionId;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

    public function setMensaje(string $mensaje): self
    {
        $this->mensaje = $mensaje;

        return $this;
    }

    public function getLeido(): ?string
    {
        return $this->leido;
    }

    public function setLeido(string $leido): self
    {
        $this->leido = $leido;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    public function getRedireccion(): ?string
    {
        return $this->redireccion;
    }

    public function setRedireccion(string $redireccion): self
    {
        $this->redireccion = $redireccion;

        return $this;
    }

    public function getUsuario(): ?User
    {
        return $this->usuario;
    }

    public function setUsuario(?User $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }


}
