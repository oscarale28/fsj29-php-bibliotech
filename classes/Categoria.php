<?php

require_once 'EntidadBase.php';

/**
 * Clase Categoria
 * Representa una categoría de libros en el sistema
 * Implementa encapsulación y herencia
 */
class Categoria extends EntidadBase
{
    private string $nombre;
    private string $descripcion;
    private string $codigo;
    private bool $activa;

    /**
     * Constructor de la clase Categoria
     */
    public function __construct(
        string $nombre,
        string $descripcion = '',
        string $codigo = '',
        bool $activa = true,
        int $id = 0
    ) {
        parent::__construct($id);
        $this->nombre = $nombre;
        $this->descripcion = $descripcion;
        $this->codigo = $codigo ?: $this->generarCodigo($nombre);
        $this->activa = $activa;
    }

    // Getters y Setters
    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void
    {
        if (!empty(trim($nombre))) {
            $this->nombre = trim($nombre);
            $this->actualizarFechaModificacion();
        }
    }

    public function getDescripcion(): string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void
    {
        $this->descripcion = trim($descripcion);
        $this->actualizarFechaModificacion();
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): void
    {
        $this->codigo = strtoupper(trim($codigo));
        $this->actualizarFechaModificacion();
    }

    public function isActiva(): bool
    {
        return $this->activa;
    }

    public function setActiva(bool $activa): void
    {
        $this->activa = $activa;
        $this->actualizarFechaModificacion();
    }

    /**
     * Activa la categoría
     */
    public function activar(): void
    {
        $this->setActiva(true);
    }

    /**
     * Desactiva la categoría
     */
    public function desactivar(): void
    {
        $this->setActiva(false);
    }

    /**
     * Genera un código automático basado en el nombre
     */
    private function generarCodigo(string $nombre): string
    {
        $palabras = explode(' ', strtoupper(trim($nombre)));
        $codigo = '';

        foreach ($palabras as $palabra) {
            if (!empty($palabra)) {
                $codigo .= substr($palabra, 0, 3);
            }
        }

        return substr($codigo, 0, 10);
    }

    /**
     * Valida los datos de la categoría
     */
    protected function validar(): bool
    {
        return !empty($this->nombre) && !empty($this->codigo);
    }

    /**
     * Convierte la categoría a array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'codigo' => $this->codigo,
            'activa' => $this->activa,
            'fecha_creacion' => $this->fechaCreacion->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->fechaActualizacion->format('Y-m-d H:i:s')
        ];
    }

    public function __toString(): string
    {
        return $this->nombre . " ({$this->codigo})";
    }
}
