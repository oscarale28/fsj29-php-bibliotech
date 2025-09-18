# 🏛️ BiblioTech - Sistema de Gestión de Biblioteca

## 📋 Descripción del Proyecto

BiblioTech es un sistema completo de gestión de biblioteca desarrollado en PHP utilizando **Programación Orientada a Objetos (POO)**. El sistema cuenta con una interfaz web moderna y funcional que permite gestionar libros, autores, categorías, usuarios y préstamos de manera eficiente, cumpliendo con todos los requisitos establecidos.

### ✅ Funcionalidades Principales
- **Búsqueda de libros** por título, autor o categoría ✨
- **Sistema de préstamos** con validaciones y control de disponibilidad ✨
- **Gestión completa** de libros, autores, categorías y usuarios ✨
- **Operaciones CRUD** para todas las entidades ✨
- **Interfaz web moderna** con experiencia de usuario optimizada ✨
- **Estadísticas y reportes** del sistema en tiempo real ✨

## 🏗️ Arquitectura del Sistema

### 📁 Estructura del Proyecto
```
bibliotech/
├── 📁 src/                          # Código fuente principal
│   ├── 📁 interfaces/               # Interfaces del sistema
│   │   ├── IGestionable.php         # Interface para operaciones CRUD
│   │   └── IPrestable.php           # Interface para préstamos
│   ├── 📁 classes/                  # Entidades principales
│   │   ├── EntidadBase.php          # Clase base abstracta
│   │   ├── Autor.php                # Gestión de autores
│   │   ├── Categoria.php            # Gestión de categorías
│   │   ├── Usuario.php              # Gestión de usuarios
│   │   ├── Libro.php                # Gestión de libros (implementa IPrestable)
│   │   ├── Prestamo.php             # Gestión de préstamos
│   │   └── Biblioteca.php           # Clase principal del sistema
│   └── 📁 managers/                 # Gestores de datos (Repository Pattern)
│       ├── BaseManager.php          # Gestor base abstracto
│       ├── AutorManager.php         # Gestor de autores
│       ├── CategoriaManager.php     # Gestor de categorías
│       ├── UsuarioManager.php       # Gestor de usuarios
│       ├── LibroManager.php         # Gestor de libros
│       └── PrestamoManager.php      # Gestor de préstamos
├── 📁 assets/                       # Recursos estáticos
│   └── 📁 css/                      # Estilos
│       └── styles.css               # Estilos principales con variables CSS
├── 📁 vendor/                       # Dependencias de Composer
├── 🔧 composer.json                 # Configuración de autoloading PSR-4
├── 🌐 index.php                     # Interfaz web principal
└── 📖 README.md                     # Documentación del proyecto
```
_Este proyecto fue desarrollado como parte del bootcamp Full Stack Jr. de Kodigo._