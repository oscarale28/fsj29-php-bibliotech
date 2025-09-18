<?php

require_once 'BaseManager.php';
require_once '../classes/Categoria.php';

/**
 * Clase CategoriaManager
 * Gestiona las operaciones CRUD para categorías
 * Implementa funcionalidades específicas para la gestión de categorías
 */
class CategoriaManager extends BaseManager
{
    /**
     * Crea una nueva categoría
     */
    public function crear(array $datos): bool
    {
        try {
            // Validar datos requeridos
            if (empty($datos['nombre'])) {
                return false;
            }

            // Verificar que no existe una categoría con el mismo código
            $codigo = $datos['codigo'] ?? '';
            if ($codigo && $this->existeCodigo($codigo)) {
                return false;
            }

            $id = $this->obtenerSiguienteId();

            $categoria = new Categoria(
                $datos['nombre'],
                $datos['descripcion'] ?? '',
                $codigo,
                $datos['activa'] ?? true,
                $id
            );

            // Verificar que el código generado no existe
            if ($this->existeCodigo($categoria->getCodigo())) {
                return false;
            }

            $this->entidades[$id] = $categoria;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Actualiza una categoría existente
     */
    public function actualizar(int $id, array $datos): bool
    {
        $categoria = $this->obtenerPorId($id);
        if (!$categoria) {
            return false;
        }

        try {
            if (isset($datos['nombre'])) {
                $categoria->setNombre($datos['nombre']);
            }

            if (isset($datos['descripcion'])) {
                $categoria->setDescripcion($datos['descripcion']);
            }

            if (isset($datos['codigo'])) {
                // Verificar que el nuevo código no existe en otra categoría
                if ($datos['codigo'] !== $categoria->getCodigo() && $this->existeCodigo($datos['codigo'])) {
                    return false;
                }
                $categoria->setCodigo($datos['codigo']);
            }

            if (isset($datos['activa'])) {
                $categoria->setActiva($datos['activa']);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Busca categorías por código
     */
    public function buscarPorCodigo(string $codigo): ?Categoria
    {
        foreach ($this->entidades as $categoria) {
            if (strtoupper($categoria->getCodigo()) === strtoupper($codigo)) {
                return $categoria;
            }
        }

        return null;
    }

    /**
     * Obtiene categorías activas
     */
    public function obtenerCategoriasActivas(): array
    {
        $resultados = [];

        foreach ($this->entidades as $categoria) {
            if ($categoria->isActiva()) {
                $resultados[] = $categoria;
            }
        }

        return $resultados;
    }

    /**
     * Obtiene categorías inactivas
     */
    public function obtenerCategoriasInactivas(): array
    {
        $resultados = [];

        foreach ($this->entidades as $categoria) {
            if (!$categoria->isActiva()) {
                $resultados[] = $categoria;
            }
        }

        return $resultados;
    }

    /**
     * Activa una categoría
     */
    public function activarCategoria(int $id): bool
    {
        $categoria = $this->obtenerPorId($id);
        if (!$categoria) {
            return false;
        }

        $categoria->activar();
        return true;
    }

    /**
     * Desactiva una categoría
     */
    public function desactivarCategoria(int $id): bool
    {
        $categoria = $this->obtenerPorId($id);
        if (!$categoria) {
            return false;
        }

        $categoria->desactivar();
        return true;
    }

    /**
     * Busca categorías por nombre parcial
     */
    public function buscarPorNombre(string $nombre): array
    {
        return $this->buscar(['nombre' => $nombre]);
    }

    /**
     * Verifica si existe una categoría con el código especificado
     */
    public function existeCodigo(string $codigo): bool
    {
        foreach ($this->entidades as $categoria) {
            if (strtoupper($categoria->getCodigo()) === strtoupper($codigo)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verifica si existe una categoría con el nombre especificado
     */
    public function existeNombre(string $nombre): bool
    {
        foreach ($this->entidades as $categoria) {
            if (strtolower($categoria->getNombre()) === strtolower($nombre)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Elimina una categoría (sobrescribe el método base)
     */
    public function eliminar(int $id): bool
    {
        $categoria = $this->obtenerPorId($id);
        if (!$categoria) {
            return false;
        }

        // Por seguridad, desactivar en lugar de eliminar si está en uso
        // En un sistema real, verificaríamos si hay libros asociados
        $categoria->desactivar();

        return parent::eliminar($id);
    }

    /**
     * Obtiene estadísticas de categorías
     */
    public function obtenerEstadisticas(): array
    {
        $total = $this->contar();
        $activas = count($this->obtenerCategoriasActivas());
        $inactivas = count($this->obtenerCategoriasInactivas());

        // Categoría más común (simulado - en un sistema real se contarían los libros)
        $masPopular = null;
        if ($total > 0) {
            $masPopular = reset($this->entidades)->getNombre();
        }

        return [
            'total_categorias' => $total,
            'categorias_activas' => $activas,
            'categorias_inactivas' => $inactivas,
            'porcentaje_activas' => $total > 0 ? round(($activas / $total) * 100, 2) : 0,
            'categoria_mas_popular' => $masPopular
        ];
    }
}
