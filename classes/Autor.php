<?php

require_once 'EntidadBase.php';

/**
 * Clase Autor
 * Representa un autor de libros en el sistema
 * Implementa encapsulación mediante propiedades privadas y métodos públicos
 */
class Autor extends EntidadBase
{
    private string $nombre;
    private string $apellido;
    private string $nacionalidad;
    private ?DateTime $fechaNacimiento;
    private ?DateTime $fechaFallecimiento;
    private string $biografia;
    
    /**
     * Constructor de la clase Autor
     * @param string $nombre Nombre del autor
     * @param string $apellido Apellido del autor
     * @param string $nacionalidad Nacionalidad del autor
     * @param DateTime|null $fechaNacimiento Fecha de nacimiento
     * @param string $biografia Biografía del autor
     * @param int $id ID del autor
     */
    public function __construct(
        string $nombre,
        string $apellido,
        string $nacionalidad = '',
        ?DateTime $fechaNacimiento = null,
        string $biografia = '',
        int $id = 0
    ) {
        parent::__construct($id);
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->nacionalidad = $nacionalidad;
        $this->fechaNacimiento = $fechaNacimiento;
        $this->fechaFallecimiento = null;
        $this->biografia = $biografia;
    }
    
    // Getters y Setters (Encapsulación)
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
    
    public function getApellido(): string
    {
        return $this->apellido;
    }
    
    public function setApellido(string $apellido): void
    {
        if (!empty(trim($apellido))) {
            $this->apellido = trim($apellido);
            $this->actualizarFechaModificacion();
        }
    }
    
    public function getNacionalidad(): string
    {
        return $this->nacionalidad;
    }
    
    public function setNacionalidad(string $nacionalidad): void
    {
        $this->nacionalidad = trim($nacionalidad);
        $this->actualizarFechaModificacion();
    }
    
    public function getFechaNacimiento(): ?DateTime
    {
        return $this->fechaNacimiento;
    }
    
    public function setFechaNacimiento(?DateTime $fechaNacimiento): void
    {
        $this->fechaNacimiento = $fechaNacimiento;
        $this->actualizarFechaModificacion();
    }
    
    public function getFechaFallecimiento(): ?DateTime
    {
        return $this->fechaFallecimiento;
    }
    
    public function setFechaFallecimiento(?DateTime $fechaFallecimiento): void
    {
        $this->fechaFallecimiento = $fechaFallecimiento;
        $this->actualizarFechaModificacion();
    }
    
    public function getBiografia(): string
    {
        return $this->biografia;
    }
    
    public function setBiografia(string $biografia): void
    {
        $this->biografia = trim($biografia);
        $this->actualizarFechaModificacion();
    }
    
    /**
     * Obtiene el nombre completo del autor
     * @return string Nombre completo
     */
    public function getNombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }
    
    /**
     * Verifica si el autor está vivo
     * @return bool True si está vivo, false si ha fallecido
     */
    public function estaVivo(): bool
    {
        return $this->fechaFallecimiento === null;
    }
    
    /**
     * Calcula la edad del autor
     * @return int|null Edad en años o null si no se puede calcular
     */
    public function calcularEdad(): ?int
    {
        if ($this->fechaNacimiento === null) {
            return null;
        }
        
        $fechaReferencia = $this->fechaFallecimiento ?? new DateTime();
        return $this->fechaNacimiento->diff($fechaReferencia)->y;
    }
    
    /**
     * Valida los datos del autor
     * @return bool True si los datos son válidos
     */
    protected function validar(): bool
    {
        return !empty($this->nombre) && !empty($this->apellido);
    }
    
    /**
     * Convierte el autor a array
     * @return array Representación en array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_completo' => $this->getNombreCompleto(),
            'nacionalidad' => $this->nacionalidad,
            'fecha_nacimiento' => $this->fechaNacimiento?->format('Y-m-d'),
            'fecha_fallecimiento' => $this->fechaFallecimiento?->format('Y-m-d'),
            'biografia' => $this->biografia,
            'esta_vivo' => $this->estaVivo(),
            'edad' => $this->calcularEdad(),
            'fecha_creacion' => $this->fechaCreacion->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->fechaActualizacion->format('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Representación en string del autor
     * @return string Cadena representativa
     */
    public function __toString(): string
    {
        return $this->getNombreCompleto() . ($this->nacionalidad ? " ({$this->nacionalidad})" : '');
    }
}
