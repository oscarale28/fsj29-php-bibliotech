# 🏛️ BiblioTech - Sistema de Gestión de Biblioteca

## 📋 Descripción del Proyecto

BiblioTech es un sistema completo de gestión de biblioteca desarrollado en PHP utilizando **Programación Orientada a Objetos (POO)**. El sistema permite gestionar libros, autores, categorías, usuarios y préstamos de manera eficiente, cumpliendo con todos los requisitos establecidos.

## 🎯 Requisitos Cumplidos

### ✅ Funcionalidades Principales
- **Búsqueda de libros** por título, autor o categoría
- **Sistema de préstamos** con validaciones y control de disponibilidad
- **Gestión completa** de libros, autores, categorías y usuarios
- **Operaciones CRUD** para todas las entidades
- **Sistema de multas** por retrasos en devoluciones
- **Estadísticas y reportes** del sistema

### ✅ Pilares de POO Implementados

1. **Encapsulación**
   - Propiedades privadas en todas las clases
   - Métodos públicos de acceso (getters/setters)
   - Validaciones internas de datos

2. **Herencia**
   - `EntidadBase`: Clase base para todas las entidades
   - `ManagerBase`: Clase base para todos los gestores
   - Reutilización de código y estructura común

3. **Polimorfismo**
   - Implementación de interfaces `IGestionable` e `IPrestable`
   - Métodos que se comportan diferente según la clase

4. **Abstracción**
   - Interfaces que definen contratos claros
   - Clases abstractas con métodos abstractos
   - Ocultación de complejidad interna

## 🏗️ Arquitectura del Sistema

### 📁 Estructura del Proyecto
```
bibliotech/
├── interfaces/
│   ├── IGestionable.php      # Interface para operaciones CRUD
│   └── IPrestable.php        # Interface para préstamos
├── classes/
│   ├── EntidadBase.php       # Clase base abstracta
│   ├── Autor.php             # Gestión de autores
│   ├── Categoria.php         # Gestión de categorías
│   ├── Usuario.php           # Gestión de usuarios
│   ├── Libro.php             # Gestión de libros (implementa IPrestable)
│   ├── Prestamo.php          # Gestión de préstamos
│   └── Biblioteca.php        # Clase principal del sistema
├── managers/
│   ├── ManagerBase.php       # Gestor base abstracto
│   ├── ManagerAutor.php      # Gestor de autores
│   ├── ManagerCategoria.php  # Gestor de categorías
│   ├── ManagerUsuario.php    # Gestor de usuarios
│   ├── ManagerLibro.php      # Gestor de libros
│   └── ManagerPrestamo.php   # Gestor de préstamos
└── index.php                 # Demo del sistema
```

### 🔧 Clases Principales

#### `Biblioteca` - Clase Principal
- **Propósito**: Punto de entrada principal del sistema
- **Funcionalidades**: Coordina todos los managers y expone las funciones principales
- **Métodos clave**:
  - `buscarLibros()`: Búsqueda general
  - `prestarLibro()`: Gestión de préstamos
  - `obtenerEstadisticasGenerales()`: Reportes del sistema

#### `Libro` - Entidad Central
- **Implementa**: `IPrestable`
- **Funcionalidades**: 
  - Control de disponibilidad
  - Gestión de ejemplares
  - Estado de préstamo
- **Validaciones**: ISBN único, ejemplares disponibles

#### `Usuario` - Gestión de Usuarios
- **Tipos**: Estudiante, Profesor, Externo
- **Límites diferenciados**:
  - Estudiante: 3 libros, 15 días
  - Profesor: 10 libros, 30 días
  - Externo: 2 libros, 7 días

## 🚀 Cómo Usar el Sistema

### 1. Inicialización
```php
$biblioteca = new Biblioteca();
```

### 2. Gestión de Entidades
```php
// Agregar autor
$biblioteca->agregarAutor("Gabriel", "García Márquez", [
    'nacionalidad' => 'Colombiano',
    'biografia' => 'Premio Nobel de Literatura'
]);

// Agregar categoría
$biblioteca->agregarCategoria("Realismo Mágico", "Literatura fantástica");

// Registrar usuario
$biblioteca->registrarUsuario("Ana", "García", "ana@email.com", "12345", "estudiante");
```

### 3. Búsqueda de Libros
```php
// Búsqueda por título
$libros = $biblioteca->buscarLibrosPorTitulo("Cien Años");

// Búsqueda por autor
$libros = $biblioteca->buscarLibrosPorAutor("García Márquez");

// Búsqueda por categoría
$libros = $biblioteca->buscarLibrosPorCategoria("Realismo Mágico");

// Búsqueda general
$libros = $biblioteca->buscarLibros("García");
```

### 4. Sistema de Préstamos
```php
// Realizar préstamo
$exito = $biblioteca->prestarLibro($usuarioId, $libroId, "Observaciones");

// Devolver libro
$exito = $biblioteca->devolverLibro($prestamoId);

// Consultar préstamos del usuario
$prestamos = $biblioteca->obtenerPrestamosUsuario($usuarioId);
```

## 📊 Características Técnicas

### Buenas Prácticas Implementadas
- **Separación de responsabilidades**: Cada clase tiene una función específica
- **Principio de responsabilidad única**: Métodos enfocados en una tarea
- **Validación de datos**: Entrada y salida controlada
- **Manejo de errores**: Try-catch y validaciones
- **Documentación**: PHPDoc en todos los métodos
- **Naming conventions**: Nombres descriptivos y consistentes

### Validaciones del Sistema
- **Usuarios únicos**: Email y número de identificación
- **ISBN únicos**: Un ISBN por libro
- **Límites de préstamo**: Según tipo de usuario
- **Disponibilidad**: Verificación antes de préstamo
- **Multas automáticas**: Cálculo por días de retraso

## 🧪 Demo y Pruebas

Ejecuta `index.php` para ver una demostración completa que incluye:
- Creación de autores, categorías y usuarios
- Agregado de libros al catálogo
- Búsquedas por diferentes criterios
- Préstamos y devoluciones
- Estadísticas del sistema
- Verificación de pilares POO

## 🎓 Objetivos de Aprendizaje Alcanzados

1. ✅ **Programación Orientada a Objetos**: Implementación completa de los 4 pilares
2. ✅ **Gestión de biblioteca**: Sistema funcional con todas las operaciones requeridas
3. ✅ **Búsquedas avanzadas**: Múltiples criterios de búsqueda implementados
4. ✅ **Sistema de préstamos**: Control completo del ciclo de vida de préstamos
5. ✅ **Buenas prácticas**: Código limpio, documentado y mantenible
6. ✅ **Arquitectura sólida**: Diseño escalable y extensible

---
*Desarrollado como proyecto educativo para demostrar el dominio de POO en PHP*
