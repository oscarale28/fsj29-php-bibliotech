<?php

require_once 'EntidadBase.php';
require_once 'Usuario.php';
require_once 'Libro.php';

/**
 * Clase Prestamo
 * Representa una transacción de préstamo de libro
 * Implementa el registro histórico de préstamos
 */
class Prestamo extends EntidadBase
{
    private Usuario $usuario;
    private Libro $libro;
    private DateTime $fechaPrestamo;
    private DateTime $fechaDevolucionEsperada;
    private ?DateTime $fechaDevolucionReal;
    private string $estado; // 'activo', 'devuelto', 'vencido'
    private string $observaciones;
    private float $multa;

    // Constantes para estados
    public const ESTADO_ACTIVO = 'activo';
    public const ESTADO_DEVUELTO = 'devuelto';
    public const ESTADO_VENCIDO = 'vencido';

    // Constante para cálculo de multa
    public const MULTA_DIARIA = 0.50; // Multa por día de retraso

    /**
     * Constructor de la clase Prestamo
     */
    public function __construct(
        Usuario $usuario,
        Libro $libro,
        int $diasPrestamo = 15,
        string $observaciones = '',
        int $id = 0
    ) {
        parent::__construct($id);
        $this->usuario = $usuario;
        $this->libro = $libro;
        $this->fechaPrestamo = new DateTime();
        $this->fechaDevolucionEsperada = (new DateTime())->add(new DateInterval("P{$diasPrestamo}D"));
        $this->fechaDevolucionReal = null;
        $this->estado = self::ESTADO_ACTIVO;
        $this->observaciones = $observaciones;
        $this->multa = 0.0;
    }

    // Getters
    public function getUsuario(): Usuario
    {
        return $this->usuario;
    }

    public function getLibro(): Libro
    {
        return $this->libro;
    }

    public function getFechaPrestamo(): DateTime
    {
        return $this->fechaPrestamo;
    }

    public function getFechaDevolucionEsperada(): DateTime
    {
        return $this->fechaDevolucionEsperada;
    }

    public function getFechaDevolucionReal(): ?DateTime
    {
        return $this->fechaDevolucionReal;
    }

    public function getEstado(): string
    {
        return $this->estado;
    }

    public function getObservaciones(): string
    {
        return $this->observaciones;
    }

    public function getMulta(): float
    {
        return $this->multa;
    }

    // Setters
    public function setObservaciones(string $observaciones): void
    {
        $this->observaciones = trim($observaciones);
        $this->actualizarFechaModificacion();
    }

    public function setMulta(float $multa): void
    {
        $this->multa = max(0, $multa);
        $this->actualizarFechaModificacion();
    }

    /**
     * Procesa la devolución del libro
     */
    public function devolver(): bool
    {
        if ($this->estado !== self::ESTADO_ACTIVO) {
            return false;
        }

        $this->fechaDevolucionReal = new DateTime();
        $this->estado = self::ESTADO_DEVUELTO;

        // Calcular multa si hay retraso
        if ($this->estaVencido()) {
            $this->calcularMulta();
        }

        $this->actualizarFechaModificacion();
        return $this->libro->devolver();
    }

    /**
     * Verifica si el préstamo está vencido
     */
    public function estaVencido(): bool
    {
        if ($this->estado === self::ESTADO_DEVUELTO) {
            return $this->fechaDevolucionReal > $this->fechaDevolucionEsperada;
        }

        return new DateTime() > $this->fechaDevolucionEsperada;
    }

    /**
     * Calcula los días de retraso
     */
    public function diasRetraso(): int
    {
        if (!$this->estaVencido()) {
            return 0;
        }

        $fechaReferencia = $this->fechaDevolucionReal ?? new DateTime();
        return $fechaReferencia->diff($this->fechaDevolucionEsperada)->days;
    }

    /**
     * Calcula la multa por retraso
     */
    private function calcularMulta(): void
    {
        $diasRetraso = $this->diasRetraso();
        $this->multa = $diasRetraso * self::MULTA_DIARIA;
    }

    /**
     * Actualiza el estado del préstamo
     */
    public function actualizarEstado(): void
    {
        if ($this->estado === self::ESTADO_ACTIVO && $this->estaVencido()) {
            $this->estado = self::ESTADO_VENCIDO;
            $this->calcularMulta();
            $this->actualizarFechaModificacion();
        }
    }

    /**
     * Extiende la fecha de devolución
     */
    public function extenderPrestamo(int $diasAdicionales): bool
    {
        if ($this->estado !== self::ESTADO_ACTIVO) {
            return false;
        }

        $this->fechaDevolucionEsperada->add(new DateInterval("P{$diasAdicionales}D"));

        // Si ya no está vencido, cambiar estado a activo
        if (!$this->estaVencido()) {
            $this->estado = self::ESTADO_ACTIVO;
            $this->multa = 0.0;
        }

        $this->actualizarFechaModificacion();
        return true;
    }

    /**
     * Verifica si el préstamo está activo
     */
    public function estaActivo(): bool
    {
        return $this->estado === self::ESTADO_ACTIVO;
    }

    /**
     * Verifica si el préstamo fue devuelto
     */
    public function fueDevuelto(): bool
    {
        return $this->estado === self::ESTADO_DEVUELTO;
    }

    /**
     * Obtiene un resumen del préstamo
     */
    public function getResumen(): string
    {
        $estado = ucfirst($this->estado);
        $diasRetraso = $this->diasRetraso();
        $multa = $this->multa > 0 ? " - Multa: $" . number_format($this->multa, 2) : '';

        return "Préstamo #{$this->id} - {$this->libro->getTitulo()} - {$estado}" .
            ($diasRetraso > 0 ? " ({$diasRetraso} días de retraso)" : '') . $multa;
    }

    /**
     * Valida los datos del préstamo
     */
    protected function validar(): bool
    {
        return $this->usuario !== null &&
            $this->libro !== null &&
            $this->fechaDevolucionEsperada > $this->fechaPrestamo;
    }

    /**
     * Convierte el préstamo a array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'usuario' => $this->usuario->toArray(),
            'libro' => $this->libro->toArray(),
            'fecha_prestamo' => $this->fechaPrestamo->format('Y-m-d H:i:s'),
            'fecha_devolucion_esperada' => $this->fechaDevolucionEsperada->format('Y-m-d H:i:s'),
            'fecha_devolucion_real' => $this->fechaDevolucionReal?->format('Y-m-d H:i:s'),
            'estado' => $this->estado,
            'observaciones' => $this->observaciones,
            'multa' => $this->multa,
            'esta_vencido' => $this->estaVencido(),
            'dias_retraso' => $this->diasRetraso(),
            'resumen' => $this->getResumen(),
            'fecha_creacion' => $this->fechaCreacion->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->fechaActualizacion->format('Y-m-d H:i:s')
        ];
    }

    public function __toString(): string
    {
        return $this->getResumen();
    }
}
