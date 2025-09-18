<?php

require_once 'BaseManager.php';
require_once '../classes/Usuario.php';

/**
 * Clase UsuarioManager
 * Gestiona las operaciones CRUD para usuarios
 * Implementa funcionalidades específicas para la gestión de usuarios
 */
class UsuarioManager extends BaseManager
{
    /**
     * Crea un nuevo usuario
     */
    public function crear(array $datos): bool
    {
        try {
            // Validar datos requeridos
            if (
                empty($datos['nombre']) || empty($datos['apellido']) ||
                empty($datos['email']) || empty($datos['numero_identificacion'])
            ) {
                return false;
            }

            // Verificar que no existe un usuario con el mismo número de identificación
            if ($this->existeNumeroIdentificacion($datos['numero_identificacion'])) {
                return false;
            }

            $id = $this->obtenerSiguienteId();

            $usuario = new Usuario(
                $datos['nombre'],
                $datos['apellido'],
                $datos['email'],
                $datos['numero_identificacion'],
                $datos['tipo_usuario'] ?? Usuario::TIPO_ESTUDIANTE,
                $datos['telefono'] ?? '',
                $datos['direccion'] ?? '',
                $datos['activo'] ?? true,
                $id
            );

            $this->entidades[$id] = $usuario;
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Actualiza un usuario existente
     */
    public function actualizar(int $id, array $datos): bool
    {
        $usuario = $this->obtenerPorId($id);
        if (!$usuario) {
            return false;
        }

        try {
            if (isset($datos['nombre'])) {
                $usuario->setNombre($datos['nombre']);
            }

            if (isset($datos['apellido'])) {
                $usuario->setApellido($datos['apellido']);
            }

            if (isset($datos['email'])) {
                // Verificar que el nuevo email no existe en otro usuario
                if ($datos['email'] !== $usuario->getEmail()) {
                    return false;
                }
                $usuario->setEmail($datos['email']);
            }

            if (isset($datos['telefono'])) {
                $usuario->setTelefono($datos['telefono']);
            }

            if (isset($datos['direccion'])) {
                $usuario->setDireccion($datos['direccion']);
            }

            if (isset($datos['numero_identificacion'])) {
                // Verificar que el nuevo número no existe en otro usuario
                if (
                    $datos['numero_identificacion'] !== $usuario->getNumeroIdentificacion() &&
                    $this->existeNumeroIdentificacion($datos['numero_identificacion'])
                ) {
                    return false;
                }
                $usuario->setNumeroIdentificacion($datos['numero_identificacion']);
            }

            if (isset($datos['tipo_usuario'])) {
                $usuario->setTipoUsuario($datos['tipo_usuario']);
            }

            if (isset($datos['activo'])) {
                $usuario->setActivo($datos['activo']);
            }

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Busca usuario por email
     */
    public function buscarPorEmail(string $email): ?Usuario
    {
        foreach ($this->entidades as $usuario) {
            if (strtolower($usuario->getEmail()) === strtolower($email)) {
                return $usuario;
            }
        }

        return null;
    }

    /**
     * Busca usuario por número de identificación
     */
    public function buscarPorNumeroIdentificacion(string $numeroIdentificacion): ?Usuario
    {
        foreach ($this->entidades as $usuario) {
            if ($usuario->getNumeroIdentificacion() === $numeroIdentificacion) {
                return $usuario;
            }
        }

        return null;
    }

    /**
     * Obtiene usuarios por tipo
     */
    public function obtenerPorTipo(string $tipo): array
    {
        return $this->buscar(['tipo_usuario' => $tipo]);
    }

    /**
     * Activa un usuario
     */
    public function activarUsuario(int $id): bool
    {
        $usuario = $this->obtenerPorId($id);
        if (!$usuario) {
            return false;
        }

        $usuario->activar();
        return true;
    }

    /**
     * Desactiva un usuario
     */
    public function desactivarUsuario(int $id): bool
    {
        $usuario = $this->obtenerPorId($id);
        if (!$usuario) {
            return false;
        }

        $usuario->desactivar();
        return true;
    }

    /**
     * Busca usuarios por nombre completo
     */
    public function buscarPorNombreCompleto(string $nombre): array
    {
        $resultados = [];

        foreach ($this->entidades as $usuario) {
            if (stripos($usuario->getNombreCompleto(), $nombre) !== false) {
                $resultados[] = $usuario;
            }
        }

        return $resultados;
    }

    /**
     * Verifica si existe un usuario con el número de identificación especificado
     */
    public function existeNumeroIdentificacion(string $numeroIdentificacion): bool
    {
        return $this->buscarPorNumeroIdentificacion($numeroIdentificacion) !== null;
    }

    /**
     * Obtiene estadísticas de usuarios
     */
    public function obtenerEstadisticas(): array
    {
        $total = $this->contar();

        // Agrupar por tipo de usuario
        $porTipo = [
            Usuario::TIPO_ESTUDIANTE => count($this->obtenerPorTipo(Usuario::TIPO_ESTUDIANTE)),
            Usuario::TIPO_PROFESOR => count($this->obtenerPorTipo(Usuario::TIPO_PROFESOR)),
            Usuario::TIPO_EXTERNO => count($this->obtenerPorTipo(Usuario::TIPO_EXTERNO))
        ];

        return [
            'total_usuarios' => $total,
            'por_tipo' => $porTipo,
            'tipo_mas_comun' => $porTipo ? array_keys($porTipo, max($porTipo))[0] : null
        ];
    }
}
