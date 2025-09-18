<?php

require_once 'EntidadBase.php';
require_once '../interfaces/IPrestable.php';
require_once 'Autor.php';
require_once 'Categoria.php';

/**
 * Clase Libro
 * Representa un libro en el sistema de biblioteca
 * Implementa la interfaz IPrestable y hereda de EntidadBase
 */
class Libro extends EntidadBase implements IPrestable
{
    private string $titulo;
    private string $isbn;
    private Autor $autor;
    private Categoria $categoria;
    private string $editorial;
    private int $anioPublicacion;
    private int $numeroPaginas;
    private string $idioma;
    private string $descripcion;
    private int $ejemplaresTotal;
    private int $ejemplaresDisponibles;
    private string $ubicacion;
    private bool $activo;

    // Estados de préstamo
    private bool $prestado;
    private ?int $usuarioPrestamo;
    private ?DateTime $fechaPrestamo;
    private ?DateTime $fechaDevolucionEsperada;

    /**
     * Constructor de la clase Libro
     */
    public function __construct(
        string $titulo,
        string $isbn,
        Autor $autor,
        Categoria $categoria,
        string $editorial = '',
        int $anioPublicacion = 0,
        int $numeroPaginas = 0,
        string $idioma = 'Español',
        string $descripcion = '',
        int $ejemplaresTotal = 1,
        string $ubicacion = '',
        int $id = 0
    ) {
        parent::__construct($id);
        $this->titulo = $titulo;
        $this->isbn = $isbn;
        $this->autor = $autor;
        $this->categoria = $categoria;
        $this->editorial = $editorial;
        $this->anioPublicacion = $anioPublicacion;
        $this->numeroPaginas = $numeroPaginas;
        $this->idioma = $idioma;
        $this->descripcion = $descripcion;
        $this->ejemplaresTotal = $ejemplaresTotal;
        $this->ejemplaresDisponibles = $ejemplaresTotal;
        $this->ubicacion = $ubicacion;
        $this->activo = true;

        // Estado de préstamo
        $this->prestado = false;
        $this->usuarioPrestamo = null;
        $this->fechaPrestamo = null;
        $this->fechaDevolucionEsperada = null;
    }

    // Getters y Setters
    public function getTitulo(): string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): void
    {
        if (!empty(trim($titulo))) {
            $this->titulo = trim($titulo);
            $this->actualizarFechaModificacion();
        }
    }

    public function getIsbn(): string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): void
    {
        $this->isbn = trim($isbn);
        $this->actualizarFechaModificacion();
    }

    public function getAutor(): Autor
    {
        return $this->autor;
    }

    public function setAutor(Autor $autor): void
    {
        $this->autor = $autor;
        $this->actualizarFechaModificacion();
    }

    public function getCategoria(): Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(Categoria $categoria): void
    {
        $this->categoria = $categoria;
        $this->actualizarFechaModificacion();
    }

    public function getEditorial(): string
    {
        return $this->editorial;
    }

    public function setEditorial(string $editorial): void
    {
        $this->editorial = trim($editorial);
        $this->actualizarFechaModificacion();
    }

    public function getAnioPublicacion(): int
    {
        return $this->anioPublicacion;
    }

    public function setAnioPublicacion(int $anioPublicacion): void
    {
        if ($anioPublicacion > 0 && $anioPublicacion <= date('Y')) {
            $this->anioPublicacion = $anioPublicacion;
            $this->actualizarFechaModificacion();
        }
    }

    public function getNumeroPaginas(): int
    {
        return $this->numeroPaginas;
    }

    public function setNumeroPaginas(int $numeroPaginas): void
    {
        if ($numeroPaginas > 0) {
            $this->numeroPaginas = $numeroPaginas;
            $this->actualizarFechaModificacion();
        }
    }

    public function getIdioma(): string
    {
        return $this->idioma;
    }

    public function setIdioma(string $idioma): void
    {
        $this->idioma = trim($idioma);
        $this->actualizarFechaModificacion();
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

    public function getEjemplaresTotal(): int
    {
        return $this->ejemplaresTotal;
    }

    public function setEjemplaresTotal(int $ejemplaresTotal): void
    {
        if ($ejemplaresTotal > 0) {
            $diferencia = $ejemplaresTotal - $this->ejemplaresTotal;
            $this->ejemplaresTotal = $ejemplaresTotal;
            $this->ejemplaresDisponibles += $diferencia;

            // No permitir ejemplares disponibles negativos
            if ($this->ejemplaresDisponibles < 0) {
                $this->ejemplaresDisponibles = 0;
            }

            $this->actualizarFechaModificacion();
        }
    }

    public function getEjemplaresDisponibles(): int
    {
        return $this->ejemplaresDisponibles;
    }

    public function getUbicacion(): string
    {
        return $this->ubicacion;
    }

    public function setUbicacion(string $ubicacion): void
    {
        $this->ubicacion = trim($ubicacion);
        $this->actualizarFechaModificacion();
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

    public function isPrestado(): bool
    {
        return $this->prestado;
    }

    public function getUsuarioPrestamo(): ?int
    {
        return $this->usuarioPrestamo;
    }

    public function getFechaPrestamo(): ?DateTime
    {
        return $this->fechaPrestamo;
    }

    public function getFechaDevolucionEsperada(): ?DateTime
    {
        return $this->fechaDevolucionEsperada;
    }

    // Implementación de IPrestable
    public function prestar(int $usuarioId): bool
    {
        if (!$this->estaDisponible()) {
            return false;
        }

        $this->prestado = true;
        $this->usuarioPrestamo = $usuarioId;
        $this->fechaPrestamo = new DateTime();
        $this->fechaDevolucionEsperada = (new DateTime())->add(new DateInterval('P15D')); // 15 días
        $this->ejemplaresDisponibles--;
        $this->actualizarFechaModificacion();

        return true;
    }

    public function devolver(): bool
    {
        if (!$this->prestado) {
            return false;
        }

        $this->prestado = false;
        $this->usuarioPrestamo = null;
        $this->fechaPrestamo = null;
        $this->fechaDevolucionEsperada = null;
        $this->ejemplaresDisponibles++;
        $this->actualizarFechaModificacion();

        return true;
    }

    public function estaDisponible(): bool
    {
        return $this->activo && $this->ejemplaresDisponibles > 0;
    }

    /**
     * Verifica si el préstamo está vencido
     */
    public function estaVencido(): bool
    {
        if (!$this->prestado || !$this->fechaDevolucionEsperada) {
            return false;
        }

        return new DateTime() > $this->fechaDevolucionEsperada;
    }

    /**
     * Calcula los días de retraso en la devolución
     */
    public function diasRetraso(): int
    {
        if (!$this->estaVencido()) {
            return 0;
        }

        $hoy = new DateTime();
        return $hoy->diff($this->fechaDevolucionEsperada)->days;
    }

    /**
     * Obtiene información completa del libro
     */
    public function getInformacionCompleta(): string
    {
        return "{$this->titulo} por {$this->autor->getNombreCompleto()} ({$this->anioPublicacion})";
    }

    /**
     * Valida los datos del libro
     */
    protected function validar(): bool
    {
        return !empty($this->titulo) &&
            !empty($this->isbn) &&
            $this->ejemplaresTotal > 0 &&
            $this->ejemplaresDisponibles >= 0 &&
            $this->ejemplaresDisponibles <= $this->ejemplaresTotal;
    }

    /**
     * Convierte el libro a array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'isbn' => $this->isbn,
            'autor' => $this->autor->toArray(),
            'categoria' => $this->categoria->toArray(),
            'editorial' => $this->editorial,
            'anio_publicacion' => $this->anioPublicacion,
            'numero_paginas' => $this->numeroPaginas,
            'idioma' => $this->idioma,
            'descripcion' => $this->descripcion,
            'ejemplares_total' => $this->ejemplaresTotal,
            'ejemplares_disponibles' => $this->ejemplaresDisponibles,
            'ubicacion' => $this->ubicacion,
            'activo' => $this->activo,
            'prestado' => $this->prestado,
            'usuario_prestamo' => $this->usuarioPrestamo,
            'fecha_prestamo' => $this->fechaPrestamo?->format('Y-m-d H:i:s'),
            'fecha_devolucion_esperada' => $this->fechaDevolucionEsperada?->format('Y-m-d H:i:s'),
            'esta_disponible' => $this->estaDisponible(),
            'esta_vencido' => $this->estaVencido(),
            'dias_retraso' => $this->diasRetraso(),
            'informacion_completa' => $this->getInformacionCompleta(),
            'fecha_creacion' => $this->fechaCreacion->format('Y-m-d H:i:s'),
            'fecha_actualizacion' => $this->fechaActualizacion->format('Y-m-d H:i:s')
        ];
    }

    public function __toString(): string
    {
        return $this->getInformacionCompleta();
    }
}
