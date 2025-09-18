<?php

namespace BiblioTech\classes;

/**
 * Clase Usuario
 * Representa un usuario del sistema de biblioteca
 * Implementa encapsulación y herencia
 */
class Usuario extends EntidadBase
{
    private string $nombre;
    private string $apellido;
    private string $email;
    private string $telefono;
    private string $direccion;
    private string $numeroIdentificacion;
    private string $tipoUsuario; // 'estudiante', 'profesor', 'externo'
    private bool $activo;
    private int $maxLibrosPrestamo;
    private int $diasMaximoPrestamo;

    // Constantes para tipos de usuario
    public const TIPO_ESTUDIANTE = 'estudiante';
    public const TIPO_PROFESOR = 'profesor';
    public const TIPO_EXTERNO = 'externo';

    /**
     * Constructor de la clase Usuario
     */
    public function __construct(
        string $nombre,
        string $apellido,
        string $email,
        string $numeroIdentificacion,
        string $tipoUsuario = self::TIPO_ESTUDIANTE,
        string $telefono = '',
        string $direccion = '',
        bool $activo = true,
        int $id = 0
    ) {
        parent::__construct($id);
        $this->nombre = $nombre;
        $this->apellido = $apellido;
        $this->email = $email;
        $this->numeroIdentificacion = $numeroIdentificacion;
        $this->tipoUsuario = $tipoUsuario;
        $this->telefono = $telefono;
        $this->direccion = $direccion;
        $this->activo = $activo;

        // Configurar límites según tipo de usuario
        $this->configurarLimitesPorTipo();
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

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = strtolower(trim($email));
            $this->actualizarFechaModificacion();
        }
    }

    public function getTelefono(): string
    {
        return $this->telefono;
    }

    public function setTelefono(string $telefono): void
    {
        $this->telefono = trim($telefono);
        $this->actualizarFechaModificacion();
    }

    public function getDireccion(): string
    {
        return $this->direccion;
    }

    public function setDireccion(string $direccion): void
    {
        $this->direccion = trim($direccion);
        $this->actualizarFechaModificacion();
    }

    public function getNumeroIdentificacion(): string
    {
        return $this->numeroIdentificacion;
    }

    public function setNumeroIdentificacion(string $numeroIdentificacion): void
    {
        $this->numeroIdentificacion = trim($numeroIdentificacion);
        $this->actualizarFechaModificacion();
    }

    public function getTipoUsuario(): string
    {
        return $this->tipoUsuario;
    }

    public function setTipoUsuario(string $tipoUsuario): void
    {
        $tiposValidos = [self::TIPO_ESTUDIANTE, self::TIPO_PROFESOR, self::TIPO_EXTERNO];
        if (in_array($tipoUsuario, $tiposValidos)) {
            $this->tipoUsuario = $tipoUsuario;
            $this->configurarLimitesPorTipo();
            $this->actualizarFechaModificacion();
        }
    }

    public function isActivo(): bool
    {
        return $this->activo;
    }

    public function setActivo(bool $activo): void
    {
        $this->activo = $activo;
        $this->actualizarFechaModificacion();
    }

    public function getMaxLibrosPrestamo(): int
    {
        return $this->maxLibrosPrestamo;
    }

    public function getDiasMaximoPrestamo(): int
    {
        return $this->diasMaximoPrestamo;
    }

    /**
     * Obtiene el nombre completo del usuario
     */
    public function getNombreCompleto(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    /**
     * Activa el usuario
     */
    public function activar(): void
    {
        $this->setActivo(true);
    }

    /**
     * Desactiva el usuario
     */
    public function desactivar(): void
    {
        $this->setActivo(false);
    }

    /**
     * Verifica si el usuario es estudiante
     */
    public function esEstudiante(): bool
    {
        return $this->tipoUsuario === self::TIPO_ESTUDIANTE;
    }

    /**
     * Verifica si el usuario es profesor
     */
    public function esProfesor(): bool
    {
        return $this->tipoUsuario === self::TIPO_PROFESOR;
    }

    /**
     * Verifica si el usuario es externo
     */
    public function esExterno(): bool
    {
        return $this->tipoUsuario === self::TIPO_EXTERNO;
    }

    /**
     * Configura los límites de préstamo según el tipo de usuario
     */
    private function configurarLimitesPorTipo(): void
    {
        switch ($this->tipoUsuario) {
            case self::TIPO_ESTUDIANTE:
                $this->maxLibrosPrestamo = 3;
                $this->diasMaximoPrestamo = 15;
                break;
            case self::TIPO_PROFESOR:
                $this->maxLibrosPrestamo = 10;
                $this->diasMaximoPrestamo = 30;
                break;
            case self::TIPO_EXTERNO:
                $this->maxLibrosPrestamo = 2;
                $this->diasMaximoPrestamo = 7;
                break;
            default:
                $this->maxLibrosPrestamo = 1;
                $this->diasMaximoPrestamo = 7;
        }
    }

    /**
     * Valida los datos del usuario
     */
    protected function validar(): bool
    {
        return !empty($this->nombre) &&
            !empty($this->apellido) &&
            !empty($this->email) &&
            filter_var($this->email, FILTER_VALIDATE_EMAIL) &&
            !empty($this->numeroIdentificacion);
    }

    /**
     * Convierte el usuario a array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_completo' => $this->getNombreCompleto(),
            'email' => $this->email,
            'telefono' => $this->telefono,
            'direccion' => $this->direccion,
            'numero_identificacion' => $this->numeroIdentificacion,
            'tipo_usuario' => $this->tipoUsuario,
            'activo' => $this->activo,
            'max_libros_prestamo' => $this->maxLibrosPrestamo,
            'dias_maximo_prestamo' => $this->diasMaximoPrestamo,
            'fecha_creacion' => $this->fechaCreacion->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->fechaActualizacion->format('Y-m-d H:i:s')
        ];
    }

    public function __toString(): string
    {
        return $this->getNombreCompleto() . " ({$this->tipoUsuario})";
    }
}
