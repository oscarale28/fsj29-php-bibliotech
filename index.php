<?php

require __DIR__ . '/vendor/autoload.php';

use BiblioTech\classes\Biblioteca;
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Iniciar sesión
session_start();

$biblioteca = new Biblioteca();

// Agregar datos de ejemplo
$biblioteca->agregarAutor("Gabriel", "García Márquez", [
    'nacionalidad' => 'Colombiano',
    'fecha_nacimiento' => '1927-03-06',
    'biografia' => 'Escritor colombiano, Premio Nobel de Literatura 1982'
]);

$biblioteca->agregarAutor("Isabel", "Allende", [
    'nacionalidad' => 'Chilena',
    'fecha_nacimiento' => '1942-08-02'
]);

$biblioteca->agregarAutor("Mario", "Vargas Llosa", [
    'nacionalidad' => 'Peruano',
    'fecha_nacimiento' => '1936-03-28'
]);

$biblioteca->agregarCategoria("Realismo Mágico", "Literatura que combina elementos fantásticos con realidad");
$biblioteca->agregarCategoria("Novela Histórica", "Narrativa ambientada en el pasado");
$biblioteca->agregarCategoria("Literatura Contemporánea", "Obras literarias actuales");

$biblioteca->registrarUsuario("Ana", "García", "ana.garcia@email.com", "12345678", "estudiante");
$biblioteca->registrarUsuario("Carlos", "López", "carlos.lopez@email.com", "87654321", "profesor");
$biblioteca->registrarUsuario("María", "Rodríguez", "maria.rodriguez@email.com", "11223344", "externo");

$autores = $biblioteca->obtenerTodosLosAutores();
$categorias = $biblioteca->obtenerCategoriasActivas();

if (!empty($autores) && !empty($categorias)) {
    $biblioteca->agregarLibro(
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

    $biblioteca->agregarLibro(
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

    $biblioteca->agregarLibro(
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

$_SESSION['biblioteca'] = serialize($biblioteca);
$_SESSION['biblioteca_inicializada'] = true;

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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #667eea;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 1.1em;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon {
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .stat-card .number {
            font-size: 2em;
            font-weight: bold;
            color: #667eea;
        }

        .stat-card .label {
            color: #666;
            font-size: 0.9em;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .card h3 {
            color: #667eea;
            margin-bottom: 20px;
            font-size: 1.3em;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e1e1e1;
            border-radius: 8px;
            font-size: 16px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: transform 0.3s ease;
            width: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .book-list {
            max-height: 400px;
            overflow-y: auto;
        }

        .book-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
        }

        .book-title {
            font-weight: bold;
            color: #333;
            font-size: 1.1em;
        }

        .book-author {
            color: #666;
            margin: 5px 0;
        }

        .book-details {
            font-size: 0.9em;
            color: #888;
        }

        .availability {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: bold;
            margin-top: 5px;
        }

        .available {
            background: #d4edda;
            color: #155724;
        }

        .unavailable {
            background: #f8d7da;
            color: #721c24;
        }

        .poo-info {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            margin-top: 20px;
        }

        .poo-info h3 {
            color: white;
            margin-bottom: 15px;
        }

        .poo-info ul {
            list-style: none;
        }

        .poo-info li {
            padding: 5px 0;
            padding-left: 20px;
            position: relative;
        }

        .poo-info li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4CAF50;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }

            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .header h1 {
                font-size: 2em;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>🏛️ BiblioTech</h1>
            <p>Sistema de Gestión de Biblioteca - Programación Orientada a Objetos</p>
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

        <!-- Información POO -->
        <div class="poo-info">
            <h3>🏗️ Pilares de POO Implementados</h3>
            <ul>
                <li><strong>Encapsulación:</strong> Propiedades privadas con getters/setters en todas las clases</li>
                <li><strong>Herencia:</strong> EntidadBase y ManagerBase como clases padre</li>
                <li><strong>Polimorfismo:</strong> Implementación de interfaces IGestionable e IPrestable</li>
                <li><strong>Abstracción:</strong> Clases abstractas y interfaces que definen contratos</li>
            </ul>

            <h3 style="margin-top: 20px;">✅ Funcionalidades Cumplidas</h3>
            <ul>
                <li>Búsqueda de libros por título, autor y categoría</li>
                <li>Sistema de préstamos con validaciones</li>
                <li>Gestión completa de entidades (CRUD)</li>
                <li>Control de disponibilidad y ejemplares</li>
                <li>Diferentes tipos de usuarios con límites</li>
                <li>Interfaz web interactiva</li>
            </ul>
        </div>
    </div>

    <script>
        // Funciones JavaScript para mejorar la experiencia de usuario
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-focus en el campo de búsqueda
            const termino = document.getElementById('termino');
            if (termino) {
                termino.focus();
            }

            // Validación del formulario de préstamo
            const formPrestamo = document.querySelector('form[action*="prestar"]');
            if (formPrestamo) {
                formPrestamo.addEventListener('submit', function(e) {
                    const usuarioId = document.getElementById('usuario_id').value;
                    const libroId = document.getElementById('libro_id').value;

                    if (!usuarioId || !libroId) {
                        e.preventDefault();
                        alert('Por favor selecciona un usuario y un libro para el préstamo.');
                    }
                });
            }

            // Animación suave para las cards
            const cards = document.querySelectorAll('.card, .stat-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.style.animation = 'fadeInUp 0.6s ease forwards';
            });

            // Resaltar libros disponibles
            const bookItems = document.querySelectorAll('.book-item');
            bookItems.forEach(item => {
                const availability = item.querySelector('.availability');
                if (availability && availability.classList.contains('available')) {
                    item.style.borderLeftColor = '#28a745';
                }
            });
        });

        // CSS Animations
        const style = document.createElement('style');
        style.textContent = `
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>

</html>