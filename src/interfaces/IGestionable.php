<?php

namespace BiblioTech\interfaces;

/**
 * Interface IGestionable
 * Define las operaciones básicas de gestión para entidades del sistema
 */
interface IGestionable
{
    /**
     * Crea una nueva entidad
     * @param array $datos Datos de la entidad
     * @return bool Éxito de la operación
     */
    public function crear(array $datos): bool;

    /**
     * Actualiza una entidad existente
     * @param int $id ID de la entidad
     * @param array $datos Nuevos datos
     * @return bool Éxito de la operación
     */
    public function actualizar(int $id, array $datos): bool;

    /**
     * Elimina una entidad
     * @param int $id ID de la entidad
     * @return bool Éxito de la operación
     */
    public function eliminar(int $id): bool;

    /**
     * Busca entidades por criterios
     * @param array $criterios Criterios de búsqueda
     * @return array Resultados encontrados
     */
    public function buscar(array $criterios): array;
}
