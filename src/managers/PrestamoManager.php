<?php

namespace BiblioTech\managers;

use BiblioTech\classes\Prestamo;
use Exception;

/**
 * Clase PrestamoManager
 * Gestiona las operaciones de préstamos de libros
 */
class PrestamoManager extends BaseManager
{
    /**
     * Crea un nuevo préstamo
     */
    public function crear(array $datos): bool
    {
        try {
            if (!isset($datos['usuario']) || !isset($datos['libro'])) {
                return false;
            }

            $usuario = $datos['usuario'];
            $libro = $datos['libro'];

            // Verificar que el libro esté disponible
            if (!$libro->estaDisponible()) {
                return false;
            }

            $id = $this->obtenerSiguienteId();
            $diasPrestamo = $datos['dias_prestamo'] ?? $usuario->getDiasMaximoPrestamo();

            $prestamo = new Prestamo(
                $usuario,
                $libro,
                $diasPrestamo,
                $datos['observaciones'] ?? '',
                $id
            );

            // Realizar el préstamo
            if ($libro->prestar($usuario->getId())) {
                $this->entidades[$id] = $prestamo;
                return true;
            }

            return false;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Actualiza un préstamo
     */
    public function actualizar(int $id, array $datos): bool
    {
        $prestamo = $this->obtenerPorId($id);
        if (!$prestamo) {
            return false;
        }

        if (isset($datos['observaciones'])) {
            $prestamo->setObservaciones($datos['observaciones']);
        }

        if (isset($datos['multa'])) {
            $prestamo->setMulta($datos['multa']);
        }

        return true;
    }

    /**
     * Procesa la devolución de un libro
     */
    public function devolverLibro(int $prestamoId): bool
    {
        $prestamo = $this->obtenerPorId($prestamoId);
        if (!$prestamo) {
            return false;
        }

        return $prestamo->devolver();
    }

    /**
     * Obtiene préstamos activos
     */
    public function obtenerPrestamosActivos(): array
    {
        $resultados = [];
        foreach ($this->entidades as $prestamo) {
            if ($prestamo->estaActivo()) {
                $resultados[] = $prestamo;
            }
        }
        return $resultados;
    }

    /**
     * Obtiene préstamos vencidos
     */
    public function obtenerPrestamosVencidos(): array
    {
        $resultados = [];
        foreach ($this->entidades as $prestamo) {
            if ($prestamo->estaVencido()) {
                $resultados[] = $prestamo;
            }
        }
        return $resultados;
    }

    /**
     * Obtiene préstamos de un usuario
     */
    public function obtenerPrestamosPorUsuario(int $usuarioId): array
    {
        $resultados = [];
        foreach ($this->entidades as $prestamo) {
            if ($prestamo->getUsuario()->getId() === $usuarioId) {
                $resultados[] = $prestamo;
            }
        }
        return $resultados;
    }

    /**
     * Extiende un préstamo
     */
    public function extenderPrestamo(int $prestamoId, int $diasAdicionales): bool
    {
        $prestamo = $this->obtenerPorId($prestamoId);
        if (!$prestamo) {
            return false;
        }

        return $prestamo->extenderPrestamo($diasAdicionales);
    }

    /**
     * Obtiene estadísticas de préstamos
     */
    public function obtenerEstadisticas(): array
    {
        $total = $this->contar();
        $activos = count($this->obtenerPrestamosActivos());
        $vencidos = count($this->obtenerPrestamosVencidos());

        $multaTotal = 0;
        foreach ($this->entidades as $prestamo) {
            $multaTotal += $prestamo->getMulta();
        }

        return [
            'total_prestamos' => $total,
            'prestamos_activos' => $activos,
            'prestamos_vencidos' => $vencidos,
            'multa_total' => $multaTotal
        ];
    }
}
