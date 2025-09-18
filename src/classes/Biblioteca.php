<?php

namespace BiblioTech\classes;

use BiblioTech\managers\AutorManager;
use BiblioTech\managers\CategoriaManager;
use BiblioTech\managers\UsuarioManager;
use BiblioTech\managers\LibroManager;
use BiblioTech\managers\PrestamoManager;

/**
 * Clase Biblioteca
 * Sistema principal de gestión de biblioteca
 * Implementa todas las funcionalidades requeridas del sistema
 */
class Biblioteca
{
    private AutorManager $managerAutor;
    private CategoriaManager $managerCategoria;
    private UsuarioManager $managerUsuario;
    private LibroManager $managerLibro;
    private PrestamoManager $managerPrestamo;

    /**
     * Constructor de la Biblioteca
     */
    public function __construct()
    {
        $this->managerAutor = new AutorManager();
        $this->managerCategoria = new CategoriaManager();
        $this->managerUsuario = new UsuarioManager();
        $this->managerLibro = new LibroManager();
        $this->managerPrestamo = new PrestamoManager();

        // Inicializar datos de ejemplo
        $this->inicializarDatosEjemplo();
    }

    /**
     * Inicializa datos de ejemplo para demostración del sistema
     */
    private function inicializarDatosEjemplo(): void
    {
        // Agregar autores de ejemplo
        $this->agregarAutor("Gabriel", "García Márquez", [
            'nacionalidad' => 'Colombiano',
            'fecha_nacimiento' => '1927-03-06',
            'biografia' => 'Escritor colombiano, Premio Nobel de Literatura 1982'
        ]);

        $this->agregarAutor("Isabel", "Allende", [
            'nacionalidad' => 'Chilena',
            'fecha_nacimiento' => '1942-08-02'
        ]);

        $this->agregarAutor("Mario", "Vargas Llosa", [
            'nacionalidad' => 'Peruano',
            'fecha_nacimiento' => '1936-03-28'
        ]);

        // Agregar categorías de ejemplo
        $this->agregarCategoria("Realismo Mágico", "Literatura que combina elementos fantásticos con realidad");
        $this->agregarCategoria("Novela Histórica", "Narrativa ambientada en el pasado");
        $this->agregarCategoria("Literatura Contemporánea", "Obras literarias actuales");

        // Registrar usuarios de ejemplo
        $this->registrarUsuario("Ana", "García", "ana.garcia@email.com", "12345678", "estudiante");
        $this->registrarUsuario("Carlos", "López", "carlos.lopez@email.com", "87654321", "profesor");
        $this->registrarUsuario("María", "Rodríguez", "maria.rodriguez@email.com", "11223344", "externo");

        // Agregar libros de ejemplo
        $autores = $this->obtenerTodosLosAutores();
        $categorias = $this->obtenerCategoriasActivas();

        if (!empty($autores) && !empty($categorias)) {
            $this->agregarLibro(
                "Cien Años de Soledad",
                "978-0-06-088328-7",
                $autores[0]->getId(),
                $categorias[0]->getId(),
                [
                    'editorial' => 'Editorial Sudamericana',
                    'anio_publicacion' => 1967,
                    'numero_paginas' => 417,
                    'ejemplares_total' => 3
                ]
            );

            $this->agregarLibro(
                "La Casa de los Espíritus",
                "978-84-204-8264-5",
                $autores[1]->getId(),
                $categorias[0]->getId(),
                [
                    'editorial' => 'Plaza & Janés',
                    'anio_publicacion' => 1982,
                    'numero_paginas' => 368,
                    'ejemplares_total' => 2
                ]
            );

            $this->agregarLibro(
                "La Ciudad y los Perros",
                "978-84-663-0002-4",
                $autores[2]->getId(),
                $categorias[1]->getId(),
                [
                    'editorial' => 'Seix Barral',
                    'anio_publicacion' => 1963,
                    'numero_paginas' => 352,
                    'ejemplares_total' => 2
                ]
            );
        }
    }

    // === GESTIÓN DE AUTORES ===

    public function agregarAutor(string $nombre, string $apellido, array $datosAdicionales = []): bool
    {
        $datos = array_merge([
            'nombre' => $nombre,
            'apellido' => $apellido
        ], $datosAdicionales);

        return $this->managerAutor->crear($datos);
    }

    public function buscarAutor(string $nombre): array
    {
        return $this->managerAutor->buscarPorNombreCompleto($nombre);
    }

    // === GESTIÓN DE CATEGORÍAS ===

    public function agregarCategoria(string $nombre, string $descripcion = ''): bool
    {
        return $this->managerCategoria->crear([
            'nombre' => $nombre,
            'descripcion' => $descripcion
        ]);
    }

    public function buscarCategoria(string $nombre): array
    {
        return $this->managerCategoria->buscarPorNombre($nombre);
    }

    // === GESTIÓN DE USUARIOS ===

    public function registrarUsuario(string $nombre, string $apellido, string $email, string $numeroId, string $tipo = 'estudiante'): bool
    {
        return $this->managerUsuario->crear([
            'nombre' => $nombre,
            'apellido' => $apellido,
            'email' => $email,
            'numero_identificacion' => $numeroId,
            'tipo_usuario' => $tipo
        ]);
    }

    public function buscarUsuario(string $criterio): array
    {
        // Buscar por nombre completo o email
        $porNombre = $this->managerUsuario->buscarPorNombreCompleto($criterio);
        $porEmail = $this->managerUsuario->buscarPorEmail($criterio);

        return $porEmail ? [$porEmail] : $porNombre;
    }

    // === GESTIÓN DE LIBROS ===

    public function agregarLibro(string $titulo, string $isbn, int $autorId, int $categoriaId, array $datosAdicionales = []): bool
    {
        $autor = $this->managerAutor->obtenerPorId($autorId);
        $categoria = $this->managerCategoria->obtenerPorId($categoriaId);

        if (!$autor || !$categoria) {
            return false;
        }

        $datos = array_merge([
            'titulo' => $titulo,
            'isbn' => $isbn,
            'autor' => $autor,
            'categoria' => $categoria
        ], $datosAdicionales);

        return $this->managerLibro->crear($datos);
    }

    public function editarLibro(int $libroId, array $nuevosDatos): bool
    {
        return $this->managerLibro->actualizar($libroId, $nuevosDatos);
    }

    public function eliminarLibro(int $libroId): bool
    {
        return $this->managerLibro->eliminar($libroId);
    }

    // === BÚSQUEDA DE LIBROS (Requisito principal) ===

    /**
     * Busca libros por título
     */
    public function buscarLibrosPorTitulo(string $titulo): array
    {
        return $this->managerLibro->buscarPorTitulo($titulo);
    }

    /**
     * Busca libros por autor
     */
    public function buscarLibrosPorAutor(string $autor): array
    {
        return $this->managerLibro->buscarPorAutor($autor);
    }

    /**
     * Busca libros por categoría
     */
    public function buscarLibrosPorCategoria(string $categoria): array
    {
        return $this->managerLibro->buscarPorCategoria($categoria);
    }

    /**
     * Búsqueda general de libros (título, autor o categoría)
     */
    public function buscarLibros(string $termino): array
    {
        $resultados = [];

        // Buscar por título
        $porTitulo = $this->buscarLibrosPorTitulo($termino);
        $resultados = array_merge($resultados, $porTitulo);

        // Buscar por autor
        $porAutor = $this->buscarLibrosPorAutor($termino);
        $resultados = array_merge($resultados, $porAutor);

        // Buscar por categoría
        $porCategoria = $this->buscarLibrosPorCategoria($termino);
        $resultados = array_merge($resultados, $porCategoria);

        // Eliminar duplicados
        return array_unique($resultados, SORT_REGULAR);
    }

    // === PRÉSTAMOS DE LIBROS (Requisito principal) ===

    /**
     * Solicita el préstamo de un libro
     */
    public function prestarLibro(int $usuarioId, int $libroId, string $observaciones = ''): bool
    {
        $usuario = $this->managerUsuario->obtenerPorId($usuarioId);
        $libro = $this->managerLibro->obtenerPorId($libroId);

        if (!$usuario || !$libro) {
            return false;
        }

        // Verificar si el usuario está activo
        if (!$usuario->isActivo()) {
            return false;
        }

        // Verificar si el libro está disponible
        if (!$libro->estaDisponible()) {
            return false;
        }

        // Verificar límites del usuario
        $prestamosActivos = $this->managerPrestamo->obtenerPrestamosPorUsuario($usuarioId);
        $prestamosActivosCount = count(array_filter($prestamosActivos, fn($p) => $p->estaActivo()));

        if ($prestamosActivosCount >= $usuario->getMaxLibrosPrestamo()) {
            return false;
        }

        return $this->managerPrestamo->crear([
            'usuario' => $usuario,
            'libro' => $libro,
            'observaciones' => $observaciones
        ]);
    }

    /**
     * Devuelve un libro prestado
     */
    public function devolverLibro(int $prestamoId): bool
    {
        return $this->managerPrestamo->devolverLibro($prestamoId);
    }

    /**
     * Obtiene los préstamos de un usuario
     */
    public function obtenerPrestamosUsuario(int $usuarioId): array
    {
        return $this->managerPrestamo->obtenerPrestamosPorUsuario($usuarioId);
    }

    /**
     * Obtiene préstamos vencidos
     */
    public function obtenerPrestamosVencidos(): array
    {
        return $this->managerPrestamo->obtenerPrestamosVencidos();
    }

    // === ESTADÍSTICAS Y REPORTES ===

    /**
     * Obtiene estadísticas generales del sistema
     */
    public function obtenerEstadisticasGenerales(): array
    {
        return [
            'autores' => $this->managerAutor->obtenerEstadisticas(),
            'categorias' => $this->managerCategoria->obtenerEstadisticas(),
            'usuarios' => $this->managerUsuario->obtenerEstadisticas(),
            'libros' => $this->managerLibro->obtenerEstadisticas(),
            'prestamos' => $this->managerPrestamo->obtenerEstadisticas()
        ];
    }

    /**
     * Obtiene un resumen del estado actual de la biblioteca
     */
    public function obtenerResumenBiblioteca(): array
    {
        $librosDisponibles = count($this->obtenerLibrosDisponibles());
        $librosTotal = $this->managerLibro->contar();
        $prestamosActivos = count($this->managerPrestamo->obtenerPrestamosActivos());
        $prestamosVencidos = count($this->obtenerPrestamosVencidos());

        return [
            'total_libros' => $librosTotal,
            'libros_disponibles' => $librosDisponibles,
            'libros_prestados' => $librosTotal - $librosDisponibles,
            'prestamos_activos' => $prestamosActivos,
            'prestamos_vencidos' => $prestamosVencidos,
            'usuarios_registrados' => $this->managerUsuario->contar(),
            'autores_registrados' => $this->managerAutor->contar(),
            'categorias_activas' => count($this->managerCategoria->obtenerCategoriasActivas())
        ];
    }

    // === MÉTODOS DE UTILIDAD ===

    /**
     * Obtiene todos los libros
     */
    public function obtenerTodosLosLibros(): array
    {
        return $this->managerLibro->obtenerTodas();
    }

    /**
     * Obtiene solo los libros disponibles
     */
    public function obtenerLibrosDisponibles(): array
    {
        $todosLosLibros = $this->obtenerTodosLosLibros();
        return array_filter($todosLosLibros, function ($libro) {
            return $libro->estaDisponible();
        });
    }

    /**
     * Obtiene todos los usuarios
     */
    public function obtenerTodosLosUsuarios(): array
    {
        return $this->managerUsuario->obtenerTodas();
    }

    /**
     * Obtiene todas las categorías activas
     */
    public function obtenerCategoriasActivas(): array
    {
        return $this->managerCategoria->obtenerCategoriasActivas();
    }

    /**
     * Obtiene todos los autores
     */
    public function obtenerTodosLosAutores(): array
    {
        return $this->managerAutor->obtenerTodas();
    }
}
