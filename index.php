<?php

require __DIR__ . '/vendor/autoload.php';

use BiblioTech\classes\Biblioteca;
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

// Crear instancia de la biblioteca (datos de ejemplo se cargan automáticamente)
$biblioteca = new Biblioteca();

// Guardar en sesión para persistencia
$_SESSION['biblioteca'] = serialize($biblioteca);

// Procesar acciones del formulario
$mensaje = '';
$resultadoBusqueda = [];

if ($_POST['accion'] ?? '' === 'buscar') {
    $termino = $_POST['termino'] ?? '';
    $tipoBusqueda = $_POST['tipo_busqueda'] ?? 'general';

    if (!empty($termino)) {
        switch ($tipoBusqueda) {
            case 'titulo':
                $resultadoBusqueda = $biblioteca->buscarLibrosPorTitulo($termino);
                break;
            case 'autor':
                $resultadoBusqueda = $biblioteca->buscarLibrosPorAutor($termino);
                break;
            case 'categoria':
                $resultadoBusqueda = $biblioteca->buscarLibrosPorCategoria($termino);
                break;
            default:
                $resultadoBusqueda = $biblioteca->buscarLibros($termino);
        }
        $mensaje = count($resultadoBusqueda) . " libro(s) encontrado(s) para '$termino'";
    }
}

if ($_POST['accion'] ?? '' === 'prestar') {
    $usuarioId = (int)($_POST['usuario_id'] ?? 0);
    $libroId = (int)($_POST['libro_id'] ?? 0);
    $observaciones = $_POST['observaciones'] ?? '';

    if ($usuarioId && $libroId) {
        $exito = $biblioteca->prestarLibro($usuarioId, $libroId, $observaciones);
        $mensaje = $exito ? 'Préstamo realizado exitosamente' : 'Error al realizar el préstamo';
        $_SESSION['biblioteca'] = serialize($biblioteca);
    }
}

$libros = $biblioteca->obtenerTodosLosLibros();
$usuarios = $biblioteca->obtenerTodosLosUsuarios();
$resumen = $biblioteca->obtenerResumenBiblioteca();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BiblioTech</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Libre+Baskerville:ital,wght@0,400;0,700;1,400&family=IBM+Plex+Mono:wght@300;400;500&display=swap" rel="stylesheet">
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🏛️ BiblioTech</h1>
            <p>Sistema de Gestión de Biblioteca</p>
        </div>

        <!-- Estadísticas -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="icon">📚</div>
                <div class="number"><?= $resumen['total_libros'] ?></div>
                <div class="label">Total de Libros</div>
            </div>
            <div class="stat-card">
                <div class="icon">✅</div>
                <div class="number"><?= $resumen['libros_disponibles'] ?></div>
                <div class="label">Disponibles</div>
            </div>
            <div class="stat-card">
                <div class="icon">📋</div>
                <div class="number"><?= $resumen['libros_prestados'] ?></div>
                <div class="label">Prestados</div>
            </div>
        </div>

        <?php if ($mensaje): ?>
            <div class="message <?= strpos($mensaje, 'Error') !== false ? 'error' : 'success' ?>">
                <?= htmlspecialchars($mensaje) ?>
            </div>
        <?php endif; ?>

        <div class="main-content">
            <!-- Búsqueda de Libros -->
            <div class="card">
                <h3>🔍 Búsqueda de Libros</h3>
                <form method="POST">
                    <input type="hidden" name="accion" value="buscar">

                    <div class="form-group">
                        <label for="termino">Término de búsqueda:</label>
                        <input type="text" id="termino" name="termino" placeholder="Título, autor o categoría..." required>
                    </div>

                    <div class="form-group">
                        <label for="tipo_busqueda">Tipo de búsqueda:</label>
                        <select id="tipo_busqueda" name="tipo_busqueda">
                            <option value="general">Búsqueda general</option>
                            <option value="titulo">Por título</option>
                            <option value="autor">Por autor</option>
                            <option value="categoria">Por categoría</option>
                        </select>
                    </div>

                    <button type="submit" class="btn">Buscar Libros</button>
                </form>

                <?php if (!empty($resultadoBusqueda)): ?>
                    <div class="book-list" style="margin-top: 20px;">
                        <?php foreach ($resultadoBusqueda as $libro): ?>
                            <div class="book-item">
                                <div class="book-title"><?= htmlspecialchars($libro->getTitulo()) ?></div>
                                <div class="book-author">Por: <?= htmlspecialchars($libro->getAutor()->getNombreCompleto()) ?></div>
                                <div class="book-details">
                                    Editorial: <?= htmlspecialchars($libro->getEditorial()) ?> |
                                    Año: <?= $libro->getAnioPublicacion() ?> |
                                    Categoría: <?= htmlspecialchars($libro->getCategoria()->getNombre()) ?>
                                </div>
                                <span class="availability <?= $libro->estaDisponible() ? 'available' : 'unavailable' ?>">
                                    <?= $libro->estaDisponible() ? 'Disponible (' . $libro->getEjemplaresDisponibles() . ')' : 'No disponible' ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sistema de Préstamos -->
            <div class="card">
                <h3>📋 Sistema de Préstamos</h3>
                <form method="POST">
                    <input type="hidden" name="accion" value="prestar">

                    <div class="form-group">
                        <label for="usuario_id">Usuario:</label>
                        <select id="usuario_id" name="usuario_id" required>
                            <option value="">Seleccionar usuario...</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?= $usuario->getId() ?>">
                                    <?= htmlspecialchars($usuario->getNombreCompleto()) ?>
                                    (<?= ucfirst($usuario->getTipoUsuario()) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="libro_id">Libro:</label>
                        <select id="libro_id" name="libro_id" required>
                            <option value="">Seleccionar libro...</option>
                            <?php foreach ($libros as $libro): ?>
                                <?php if ($libro->estaDisponible()): ?>
                                    <option value="<?= $libro->getId() ?>">
                                        <?= htmlspecialchars($libro->getTitulo()) ?> - <?= htmlspecialchars($libro->getAutor()->getNombreCompleto()) ?>
                                        (Disponibles: <?= $libro->getEjemplaresDisponibles() ?>)
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="observaciones">Observaciones:</label>
                        <textarea id="observaciones" name="observaciones" rows="3" placeholder="Motivo del préstamo..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-secondary">Realizar Préstamo</button>
                </form>
            </div>
        </div>

        <!-- Catálogo de Libros -->
        <div class="card">
            <h3>📖 Catálogo de Libros</h3>
            <div class="book-list">
                <?php foreach ($libros as $libro): ?>
                    <div class="book-item">
                        <div class="book-title"><?= htmlspecialchars($libro->getTitulo()) ?></div>
                        <div class="book-author">Por: <?= htmlspecialchars($libro->getAutor()->getNombreCompleto()) ?></div>
                        <div class="book-details">
                            Editorial: <?= htmlspecialchars($libro->getEditorial()) ?> |
                            Año: <?= $libro->getAnioPublicacion() ?> |
                            Páginas: <?= $libro->getNumeroPaginas() ?> |
                            ISBN: <?= htmlspecialchars($libro->getIsbn()) ?> |
                            Categoría: <?= htmlspecialchars($libro->getCategoria()->getNombre()) ?>
                        </div>
                        <span class="availability <?= $libro->estaDisponible() ? 'available' : 'unavailable' ?>">
                            <?= $libro->estaDisponible() ? 'Disponible (' . $libro->getEjemplaresDisponibles() . '/' . $libro->getEjemplaresTotal() . ')' : 'No disponible' ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>