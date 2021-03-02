<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Movimiento
 *
 * @ORM\Table(name="movimiento", indexes={@ORM\Index(name="cuenta_corriente_id", columns={"cuenta_corriente_id"}), @ORM\Index(name="tipo_movimiento_id", columns={"tipo_movimiento_id"})})
 * @ORM\Entity
 */
class Movimiento
{
    /**
     * @var int
     *
     * @ORM\Column(name="movimiento_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $movimientoId;

    /**
     * @var float
     *
     * @ORM\Column(name="valor", type="float", precision=10, scale=0, nullable=false)
     */
    private $valor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="f_creacion", type="datetime", nullable=false)
     */
    private $fCreacion;

    /**
     * @var string
     *
     * @ORM\Column(name="observacion", type="string", length=255, nullable=true)
     */
    private $observacion;

    /**
     * @var \CuentaCorriente
     *
     * @ORM\ManyToOne(targetEntity="CuentaCorriente",inversedBy="movimientos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cuenta_corriente_id", referencedColumnName="cuenta_corriente_id")
     * })
     */
    private $cuentaCorriente;

    /**
     * @var \TipoMovimiento
     *
     * @ORM\ManyToOne(targetEntity="TipoMovimiento")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tipo_movimiento_id", referencedColumnName="tipo_movimiento_id")
     * })
     */
    private $tipoMovimiento;

    public function __construct($cuentaCorriente,$valor,$observacion=null)
    {
        $this->cuentaCorriente = $cuentaCorriente;
        $this->fCreacion = new \DateTime();
        $this->valor = $valor;
        $this->observacion = $observacion;
    }

    public function getMovimientoId(): ?int
    {
        return $this->movimientoId;
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

    public function getFCreacion(): ?\DateTimeInterface
    {
        return $this->fCreacion;
    }

    public function setFCreacion(\DateTimeInterface $fCreacion): self
    {
        $this->fCreacion = $fCreacion;

        return $this;
    }

    public function getObservacion(): ?string
    {
        return $this->observacion;
    }

    public function setObservacion(string $observacion): self
    {
        $this->observacion = $observacion;

        return $this;
    }

    public function getCuentaCorriente(): ?CuentaCorriente
    {
        return $this->cuentaCorriente;
    }

    public function setCuentaCorriente(?CuentaCorriente $cuentaCorriente): self
    {
        $this->cuentaCorriente = $cuentaCorriente;

        return $this;
    }

    public function getTipoMovimiento(): ?TipoMovimiento
    {
        return $this->tipoMovimiento;
    }

    public function setTipoMovimiento(?TipoMovimiento $tipoMovimiento): self
    {
        $this->tipoMovimiento = $tipoMovimiento;

        return $this;
    }

}
