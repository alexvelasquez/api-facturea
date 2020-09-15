<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Comprobante
 *
 * @ORM\Table(name="comprobante")
 * @ORM\Entity
 */
class Comprobante
{
    /**
     * @var int
     *
     * @ORM\Column(name="comprobante_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $comprobanteId;

    /**
     * @var int
     *
     * @ORM\Column(name="numero", type="integer", nullable=false)
     */
    private $numero;

    /**
     * @var int
     *
     * @ORM\Column(name="punto_venta", type="integer", nullable=false)
     */
    private $puntoVenta;

    /**
     * @var int
     *
     * @ORM\Column(name="tipo_cbte", type="integer", nullable=false)
     */
    private $tipoCbte;

    public function getComprobanteId(): ?int
    {
        return $this->comprobanteId;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): self
    {
        $this->numero = $numero;

        return $this;
    }

    public function getPuntoVenta(): ?int
    {
        return $this->puntoVenta;
    }

    public function setPuntoVenta(int $puntoVenta): self
    {
        $this->puntoVenta = $puntoVenta;

        return $this;
    }

    public function getTipoCbte(): ?int
    {
        return $this->tipoCbte;
    }

    public function setTipoCbte(int $tipoCbte): self
    {
        $this->tipoCbte = $tipoCbte;

        return $this;
    }


}
