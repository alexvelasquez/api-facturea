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
     * @ORM\Column(name="mensaje", type="text", nullable=false)
     */
    private $mensaje;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function getNotificacionId(): ?int
    {
        return $this->notificacionId;
    }

    public function getMensaje(): ?string
    {
        return $this->mensaje;
    }

}
