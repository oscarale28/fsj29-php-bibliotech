<?php

namespace BiblioTech\managers;

use BiblioTech\classes\Autor;
use DateTime;
use Exception;

/**
 * Clase AutorManager
 * Gestiona las operaciones CRUD para autores
 * Implementa funcionalidades específicas para la gestión de autores
 */
class AutorManager extends BaseManager
{
    /**
     * Crea un nuevo autor
     */
    public function crear(array $datos): bool
    {
        try {
            // Validar datos requeridos
            if (empty($datos['nombre']) || empty($datos['apellido'])) {
                return false;
            }

            $id = $this->obtenerSiguienteId();

            $autor = new Autor(
                $datos['nombre'],
                $datos['apellido'],
                $datos['nacionalidad'] ?? '',
                isset($datos['fecha_nacimiento']) ? new DateTime($datos['fecha_nacimiento']) : null,
                $datos['biografia'] ?? '',
                $id
            );

            $this->entidades[$id] = $autor;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Actualiza un autor existente
     */
    public function actualizar(int $id, array $datos): bool
    {
        $autor = $this->obtenerPorId($id);
        if (!$autor) {
            return false;
        }

        try {
            if (isset($datos['nombre'])) {
                $autor->setNombre($datos['nombre']);
            }

            if (isset($datos['apellido'])) {
                $autor->setApellido($datos['apellido']);
            }

            if (isset($datos['nacionalidad'])) {
                $autor->setNacionalidad($datos['nacionalidad']);
            }

            if (isset($datos['fecha_nacimiento'])) {
                $fechaNacimiento = $datos['fecha_nacimiento'] ? new DateTime($datos['fecha_nacimiento']) : null;
                $autor->setFechaNacimiento($fechaNacimiento);
            }

            if (isset($datos['fecha_fallecimiento'])) {
                $fechaFallecimiento = $datos['fecha_fallecimiento'] ? new DateTime($datos['fecha_fallecimiento']) : null;
                $autor->setFechaFallecimiento($fechaFallecimiento);
            }

            if (isset($datos['biografia'])) {
                $autor->setBiografia($datos['biografia']);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Busca autores por nombre completo
     */
    public function buscarPorNombreCompleto(string $nombre): array
    {
        $resultados = [];

        foreach ($this->entidades as $autor) {
            if (stripos($autor->getNombreCompleto(), $nombre) !== false) {
                $resultados[] = $autor;
            }
        }

        return $resultados;
    }

    /**
     * Busca autores por nacionalidad
     */
    public function buscarPorNacionalidad(string $nacionalidad): array
    {
        return $this->buscar(['nacionalidad' => $nacionalidad]);
    }

    /**
     * Obtiene estadísticas de autores
     */
    public function obtenerEstadisticas(): array
    {
        $total = $this->contar();

        // Agrupar por nacionalidad
        $porNacionalidad = [];
        foreach ($this->entidades as $autor) {
            $nacionalidad = $autor->getNacionalidad() ?: 'Sin especificar';
            $porNacionalidad[$nacionalidad] = ($porNacionalidad[$nacionalidad] ?? 0) + 1;
        }

        // Calcular edad promedio
        $edades = [];
        foreach ($this->entidades as $autor) {
            $edad = $autor->calcularEdad();
            if ($edad !== null) {
                $edades[] = $edad;
            }
        }

        $edadPromedio = !empty($edades) ? array_sum($edades) / count($edades) : 0;

        return [
            'total_autores' => $total,
            'edad_promedio' => round($edadPromedio, 1),
            'por_nacionalidad' => $porNacionalidad
        ];
    }

    /**
     * Verifica si existe un autor con el mismo nombre y apellido
     */
    public function existeAutor(string $nombre, string $apellido): bool
    {
        foreach ($this->entidades as $autor) {
            if (
                strtolower($autor->getNombre()) === strtolower($nombre) &&
                strtolower($autor->getApellido()) === strtolower($apellido)
            ) {
                return true;
            }
        }

        return false;
    }
}
