<?php

namespace BiblioTech\classes;

use DateTime;

/**
 * Clase abstracta EntidadBase
 * Proporciona funcionalidades comunes para todas las entidades del sistema
 * Implementa encapsulación y abstracción
 */
abstract class EntidadBase
{
    protected int $id;
    protected DateTime $fechaCreacion;
    protected DateTime $fechaActualizacion;

    /**
     * Constructor de la clase base
     * @param int $id ID de la entidad
     */
    public function __construct(int $id = 0)
    {
        $this->id = $id;
        $this->fechaCreacion = new DateTime();
        $this->fechaActualizacion = new DateTime();
    }

    /**
     * Obtiene el ID de la entidad
     * @return int ID de la entidad
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Establece el ID de la entidad
     * @param int $id Nuevo ID
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Obtiene la fecha de creación
     * @return DateTime Fecha de creación
     */
    public function getFechaCreacion(): DateTime
    {
        return $this->fechaCreacion;
    }

    /**
     * Obtiene la fecha de última actualización
     * @return DateTime Fecha de actualización
     */
    public function getFechaActualizacion(): DateTime
    {
        return $this->fechaActualizacion;
    }

    /**
     * Actualiza la fecha de modificación
     */
    protected function actualizarFechaModificacion(): void
    {
        $this->fechaActualizacion = new DateTime();
    }

    /**
     * Valida los datos de la entidad
     * @return bool Validez de los datos
     */
    abstract protected function validar(): bool;

    /**
     * Convierte la entidad a array
     * @return array Representación en array
     */
    abstract public function toArray(): array;
}
