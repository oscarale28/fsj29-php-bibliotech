<?php

require_once '../interfaces/IGestionable.php';

/**
 * Clase abstracta BaseManager
 * Proporciona funcionalidades comunes para todos los gestores
 * Implementa el patrón Repository y funciones básicas de CRUD
 */
abstract class BaseManager implements IGestionable
{
    protected array $entidades;
    protected int $siguienteId;

    /**
     * Constructor del gestor base
     */
    public function __construct()
    {
        $this->entidades = [];
        $this->siguienteId = 1;
    }

    /**
     * Obtiene todas las entidades
     */
    public function obtenerTodas(): array
    {
        return $this->entidades;
    }

    /**
     * Obtiene una entidad por ID
     */
    public function obtenerPorId(int $id): ?object
    {
        return $this->entidades[$id] ?? null;
    }

    /**
     * Obtiene el conteo total de entidades
     */
    public function contar(): int
    {
        return count($this->entidades);
    }

    /**
     * Obtiene el siguiente ID disponible
     */
    protected function obtenerSiguienteId(): int
    {
        return $this->siguienteId++;
    }

    /**
     * Valida si existe una entidad con el ID especificado
     */
    protected function existeId(int $id): bool
    {
        return isset($this->entidades[$id]);
    }

    /**
     * Elimina una entidad por ID (implementación base)
     */
    public function eliminar(int $id): bool
    {
        if (!$this->existeId($id)) {
            return false;
        }

        unset($this->entidades[$id]);
        return true;
    }

    /**
     * Busca entidades que coincidan con los criterios (implementación base)
     */
    public function buscar(array $criterios): array
    {
        if (empty($criterios)) {
            return $this->entidades;
        }

        $resultados = [];

        foreach ($this->entidades as $entidad) {
            if ($this->cumpleCriterios($entidad, $criterios)) {
                $resultados[] = $entidad;
            }
        }

        return $resultados;
    }

    /**
     * Verifica si una entidad cumple con los criterios de búsqueda
     */
    protected function cumpleCriterios(object $entidad, array $criterios): bool
    {
        $entidadArray = $entidad->toArray();

        foreach ($criterios as $campo => $valor) {
            if (!isset($entidadArray[$campo])) {
                continue;
            }

            $valorEntidad = $entidadArray[$campo];

            // Búsqueda parcial para strings
            if (is_string($valorEntidad) && is_string($valor)) {
                if (stripos($valorEntidad, $valor) === false) {
                    return false;
                }
            }
            // Comparación exacta para otros tipos
            elseif ($valorEntidad !== $valor) {
                return false;
            }
        }

        return true;
    }

    /**
     * Obtiene estadísticas básicas (método abstracto)
     */
    abstract public function obtenerEstadisticas(): array;
}
