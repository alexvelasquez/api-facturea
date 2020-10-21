<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Notificacion
 *
 * @ORM\Table(name="notificacion", indexes={@ORM\Index(name="id", columns={"user_id"})})
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
     * @var string
     *
     * @ORM\Column(name="titulo", type="string", length=255, nullable=false)
     */
    private $titulo;

    /**
     * @var string
     *
     * @ORM\Column(name="mensaje", type="text", nullable=false)
     */
    private $mensaje;

    /**
     * @var string
     *
     * @ORM\Column(name="redireccion", type="string", length=255, nullable=true)
     */
    private $url;

    /**
     * @var string
     *
     * @ORM\Column(name="leido", type="string", length=1, nullable=false)
     */
    private $leido;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function __construct($titulo,$mensaje,$user,$url)
    {
        $this->titulo = $titulo;
        $this->mensaje = $mensaje;
        $this->user = $user;
        $this->url = $url;
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

    public function getLeido(): ?string
    {
        return $this->leido;
    }
    public function setLeido($value): ?string
    {
        return $this->leido=$value;
    }


}
