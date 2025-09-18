<?php

namespace BiblioTech\managers;

use BiblioTech\classes\Libro;
use Exception;

/**
 * Clase LibroManager
 * Gestiona las operaciones CRUD para libros
 * Implementa funcionalidades específicas para la gestión de libros y búsquedas avanzadas
 */
class LibroManager extends BaseManager
{
    /**
     * Crea un nuevo libro
     */
    public function crear(array $datos): bool
    {
        try {
            // Validar datos requeridos
            if (
                empty($datos['titulo']) || empty($datos['isbn']) ||
                !isset($datos['autor']) || !isset($datos['categoria'])
            ) {
                return false;
            }

            // Verificar que no existe un libro con el mismo ISBN
            if ($this->existeISBN($datos['isbn'])) {
                return false;
            }

            $id = $this->obtenerSiguienteId();

            $libro = new Libro(
                $datos['titulo'],
                $datos['isbn'],
                $datos['autor'], // Debe ser una instancia de Autor
                $datos['categoria'], // Debe ser una instancia de Categoria
                $datos['editorial'] ?? '',
                $datos['anio_publicacion'] ?? 0,
                $datos['numero_paginas'] ?? 0,
                $datos['idioma'] ?? 'Español',
                $datos['descripcion'] ?? '',
                $datos['ejemplares_total'] ?? 1,
                $datos['ubicacion'] ?? '',
                $id
            );

            $this->entidades[$id] = $libro;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Actualiza un libro existente
     */
    public function actualizar(int $id, array $datos): bool
    {
        $libro = $this->obtenerPorId($id);
        if (!$libro) {
            return false;
        }

        try {
            if (isset($datos['titulo'])) {
                $libro->setTitulo($datos['titulo']);
            }

            if (isset($datos['isbn'])) {
                // Verificar que el nuevo ISBN no existe en otro libro
                if ($datos['isbn'] !== $libro->getIsbn() && $this->existeISBN($datos['isbn'])) {
                    return false;
                }
                $libro->setIsbn($datos['isbn']);
            }

            if (isset($datos['autor'])) {
                $libro->setAutor($datos['autor']);
            }

            if (isset($datos['categoria'])) {
                $libro->setCategoria($datos['categoria']);
            }

            if (isset($datos['editorial'])) {
                $libro->setEditorial($datos['editorial']);
            }

            if (isset($datos['anio_publicacion'])) {
                $libro->setAnioPublicacion($datos['anio_publicacion']);
            }

            if (isset($datos['numero_paginas'])) {
                $libro->setNumeroPaginas($datos['numero_paginas']);
            }

            if (isset($datos['idioma'])) {
                $libro->setIdioma($datos['idioma']);
            }

            if (isset($datos['descripcion'])) {
                $libro->setDescripcion($datos['descripcion']);
            }

            if (isset($datos['ejemplares_total'])) {
                $libro->setEjemplaresTotal($datos['ejemplares_total']);
            }

            if (isset($datos['ubicacion'])) {
                $libro->setUbicacion($datos['ubicacion']);
            }

            if (isset($datos['activo'])) {
                $libro->setActivo($datos['activo']);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Busca libros por título
     */
    public function buscarPorTitulo(string $titulo): array
    {
        return $this->buscar(['titulo' => $titulo]);
    }

    /**
     * Busca libros por autor
     */
    public function buscarPorAutor(string $nombreAutor): array
    {
        $resultados = [];

        foreach ($this->entidades as $libro) {
            if (stripos($libro->getAutor()->getNombreCompleto(), $nombreAutor) !== false) {
                $resultados[] = $libro;
            }
        }

        return $resultados;
    }

    /**
     * Busca libros por categoría
     */
    public function buscarPorCategoria(string $nombreCategoria): array
    {
        $resultados = [];

        foreach ($this->entidades as $libro) {
            if (stripos($libro->getCategoria()->getNombre(), $nombreCategoria) !== false) {
                $resultados[] = $libro;
            }
        }

        return $resultados;
    }

    /**
     * Busca libros por ISBN
     */
    public function buscarPorISBN(string $isbn): ?Libro
    {
        foreach ($this->entidades as $libro) {
            if ($libro->getIsbn() === $isbn) {
                return $libro;
            }
        }

        return null;
    }

    /**
     * Verifica si existe un libro con el ISBN especificado
     */
    public function existeISBN(string $isbn): bool
    {
        return $this->buscarPorISBN($isbn) !== null;
    }

    /**
     * Obtiene estadísticas de libros
     */
    public function obtenerEstadisticas(): array
    {
        $total = $this->contar();

        // Agrupar por categoría
        $porCategoria = [];
        foreach ($this->entidades as $libro) {
            $categoria = $libro->getCategoria()->getNombre();
            $porCategoria[$categoria] = ($porCategoria[$categoria] ?? 0) + 1;
        }

        // Agrupar por autor
        $porAutor = [];
        foreach ($this->entidades as $libro) {
            $autor = $libro->getAutor()->getNombreCompleto();
            $porAutor[$autor] = ($porAutor[$autor] ?? 0) + 1;
        }

        // Calcular ejemplares totales
        $ejemplaresTotales = 0;
        $ejemplaresDisponiblesTotales = 0;

        foreach ($this->entidades as $libro) {
            $ejemplaresTotales += $libro->getEjemplaresTotal();
            $ejemplaresDisponiblesTotales += $libro->getEjemplaresDisponibles();
        }

        return [
            'total_libros' => $total,
            'ejemplares_totales' => $ejemplaresTotales,
            'ejemplares_disponibles' => $ejemplaresDisponiblesTotales,
            'ejemplares_prestados' => $ejemplaresTotales - $ejemplaresDisponiblesTotales,
            'porcentaje_disponibilidad' => $ejemplaresTotales > 0 ?
                round(($ejemplaresDisponiblesTotales / $ejemplaresTotales) * 100, 2) : 0,
            'por_categoria' => $porCategoria,
            'por_autor' => $porAutor,
            'categoria_mas_popular' => $porCategoria ? array_keys($porCategoria, max($porCategoria))[0] : null,
            'autor_mas_popular' => $porAutor ? array_keys($porAutor, max($porAutor))[0] : null
        ];
    }
}
