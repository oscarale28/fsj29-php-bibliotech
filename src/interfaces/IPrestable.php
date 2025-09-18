<?php

namespace BiblioTech\interfaces;

/**
 * Interface IPrestable
 * Define las operaciones relacionadas con préstamos
 */
interface IPrestable
{
    /**
     * Realiza el préstamo de un ítem
     * @param int $usuarioId ID del usuario
     * @return bool Éxito del préstamo
     */
    public function prestar(int $usuarioId): bool;

    /**
     * Devuelve un ítem prestado
     * @return bool Éxito de la devolución
     */
    public function devolver(): bool;

    /**
     * Verifica si el ítem está disponible para préstamo
     * @return bool Disponibilidad
     */
    public function estaDisponible(): bool;
}
