# AGENTS.md — Directrices Técnicas y Contexto para Agentes de IA

Este documento contiene las especificaciones técnicas, convenciones arquitectónicas, reglas de negocio e invariantes críticas para que cualquier modelo de Inteligencia Artificial o agente autónomo pueda interactuar, mantener y extender el proyecto **HackLib Panel**.

---

## 1. Visión General del Proyecto

**HackLib Panel** es una aplicación web monolítica construida en **PHP vanilla (sin frameworks)**, complementada con **SQLite 3**, **HTMX**, **Alpine.js** y **Tailwind CSS**. La temática visual y de experiencia de usuario corresponde a una estética **Cyberpunk / Hacker Moderno** (tonos oscuros `#0a0a0f`, acentos neón en morado `#7c3aed` y cian `#22d3ee`, fuentes monospace `JetBrains Mono` y display `Orbitron`).

El sistema actúa como una plataforma de biblioteca técnica especializada en programación, ofreciendo:
- Catálogo de libros técnicos clasificados por paradigmas, fundamentos y herramientas.
- Carrito de compras con modalidad dual (compra simulada didáctica y solicitud de préstamo físico real).
- Sistema de reservas de préstamos con emisión de códigos criptográficos únicos.
- Panel de administración para gestión de inventario, monitoreo del ciclo de vida de préstamos y administración de personal según jerarquía de roles.

---

## 2. Stack Tecnológico y Componentes

| Capa | Tecnología | Rol y Comportamiento |
|---|---|---|
| **Backend Core** | PHP 8.1+ Vanilla | Enrutador minimalista, controladores estructurados y modelos con PDO. |
| **Autoloading** | Composer (PSR-4) | Espacio de nombres `App\` mapeado a `src/`, helpers globales en `src/Helpers/functions.php`. |
| **Persistencia** | SQLite 3 con PDO | Conexión Singleton (`PRAGMA journal_mode=WAL; PRAGMA foreign_keys=ON;`). |
| **Interactividad SPA** | HTMX 2.x | Peticiones asíncronas, filtrado en tiempo real y swaps parciales de DOM (`#main-content`). |
| **Reactividad Cliente** | Alpine.js 3.x | Notificaciones toast, modales, copiado al portapapeles y estados de carga locales. |
| **Estilos** | Tailwind CSS 3.x | Compilación local mediante binario standalone (`tailwindcss-linux-x64`). |

---

## 3. Mapa de la Estructura de Directorios

```text
FinalProyect/
├── composer.json               # Configuración de dependencias y PSR-4
├── tailwind.config.js          # Configuración de tema Cyberpunk para Tailwind
├── tailwindcss                 # Binario ejecutable de Tailwind CLI (no committear)
├── fedora-run.sh               # Script de inicialización y verificación para Fedora Linux
├── start.sh                    # Script estándar de arranque
├── test_integration.php        # Suite de pruebas automatizadas end-to-end
├── README.md                   # Documentación funcional y de negocio para humanos
├── AGENTS.md                   # Documentación técnica y reglas para agentes de IA
├── config/
│   └── app.php                 # Parámetros de entorno, DB path y credenciales Master
├── database/
│   ├── schema.sql              # Definición DDL (users, books, rentals, índices)
│   ├── seed.sql                # 21 libros iniciales de programación
│   └── init.php                # Script CLI de creación/reinicio de la base de datos
├── public/
│   ├── index.php               # Front Controller y registro de rutas HTTP
│   └── css/
│       └── app.css             # Hoja de estilos minificada compilada por Tailwind
├── src/
│   ├── Router.php              # Enrutador con soporte para rutas estáticas y dinámicas {id}
│   ├── Controllers/
│   │   ├── AuthController.php    # Autenticación (login, register, logout)
│   │   ├── CatalogController.php # Catálogo, carrito, checkout y préstamos de usuario
│   │   └── AdminController.php   # Dashboard, gestión de libros, préstamos y usuarios
│   ├── Models/
│   │   ├── Database.php        # Instancia Singleton PDO
│   │   ├── User.php            # Operaciones de usuarios y roles
│   │   ├── Book.php            # Inventario, filtros y estadísticas de libros
│   │   └── Rental.php          # Ciclo de vida del préstamo y control de stock
│   ├── Middleware/
│   │   └── AuthMiddleware.php  # Control de sesiones, validación de roles y CSRF
│   └── Helpers/
│       └── functions.php       # Funciones globales (escape XSS, CSRF, flash, badges, render)
├── templates/
│   ├── layouts/
│   │   └── app.php             # Layout base (sidebar, toast container, scanlines)
│   ├── auth/                   # Vistas de acceso (login, register)
│   ├── admin/                  # Vistas administrativas (dashboard, books, rentals, users)
│   ├── user/                   # Vistas del usuario (catalog, book_detail, cart, checkout, rentals)
│   ├── partials/               # Fragmentos consumidos por HTMX (cart_items, rental_rows)
│   └── errors/
│       └── 404.php             # Pantalla de recurso no encontrado
├── storage/                    # Directorio de datos en tiempo de ejecución (SQLite)
└── tailwind/
    └── input.css               # Directivas base y utilidades personalizadas
```

---

## 4. Reglas de Negocio e Invariantes Críticas

Cualquier cambio o extensión en el código debe respetar estrictamente estas reglas:

### A. Jerarquía de Autenticación y Autorización
Existen 3 roles diferenciados en la columna `role` de la tabla `users`:
1. **`master_admin` (Super Administrador Maestro):**
   - Cuenta fija inicial: `admin` / `admin123`.
   - Poder total: puede crear, editar y eliminar libros; gestionar préstamos; y **es el único autorizado** para crear nuevos administradores y usuarios desde `/admin/users`.
   - La cuenta maestro nunca puede ser eliminada ni despojada de su rol.
2. **`admin` (Administrador Operativo):**
   - Registrado exclusivamente por el Master Admin.
   - Solo tiene facultades operativas: CRUD de libros y actualización de préstamos.
   - **Prohibido:** No tiene acceso a `/admin/users`, ni puede crear otros administradores.
3. **`user` (Cliente):**
   - Registro público desde `/register`.
   - Solo puede explorar el catálogo, buscar libros, usar el carrito (compra/préstamo) y consultar su historial en `/my-rentals`.

### B. Categorización y Atributos de Libros
Los libros se dividen obligatoriamente en 3 categorías base:
- `lenguajes`: Libros de sintaxis y paradigmas puros (ej. Rust, Ruby, Erlang, Python, Java).
- `fundamentos`: Teoría agnóstica (Estructuras de datos, Algoritmos, Patrones, Clean Code, Linux).
- `frameworks`: Herramientas y ecosistemas aplicados (Rails, Phoenix, Laravel, Docker, Kubernetes).

Campos obligatorios en la tabla `books`:
- `id`, `titulo`, `autor`, `categoria` (`lenguajes`, `fundamentos`, `frameworks`).
- `nivel` (`principiante`, `intermedio`, `avanzado`).
- `version_tecnologia` (texto descriptivo, ej. 'Rust 1.70', 'Rails 8').
- `formato` (`tapa_blanda`, `tapa_dura`, `ebook`).
- `stock` (entero >= 0), `precio` (float), `isbn` (único), `anio_publicacion` (entero).

### C. Carrito y Operaciones: Compra vs. Alquiler
- **Compra (`type = 'compra'`):**
  - Es **100% simulada**.
  - Al completar el checkout, **no modifica la base de datos** ni afecta el stock de libros. Solo emite una confirmación didáctica en pantalla.
- **Alquiler (`type = 'alquiler'`):**
  - Es **real en el inventario**.
  - Cada ítem reservado **reduce inmediatamente el `stock` en 1 unidad** de forma transaccional (`$bookModel->decrementStock()`).
  - Si un libro tiene `stock <= 0`, no puede ser añadido ni procesado para préstamo.
  - Se genera un código aleatorio único con formato `RENT-[HEX8]-[YYMMDD]` (ej. `RENT-4EB74204-261016`).
  - El código se almacena en `rentals` con estado inicial `pendiente`.

### D. Ciclo de Vida y Transición de Estados de Préstamos
Los estados válidos son: `pendiente`, `aprobado`, `prestado`, `devuelto`, `cancelado`.
- Estados que retienen stock fuera de la tienda: `['pendiente', 'aprobado', 'prestado']`.
- Estados liberados/restituidos: `['devuelto', 'cancelado']`.
- **Regla de integridad:** Si un préstamo pasa de un estado retenido a uno liberado (`devuelto` o `cancelado`), el modelo `Rental` invoca `$bookModel->incrementStock($bookId)` restaurando la unidad al almacén.
- Si por corrección administrativa pasa de liberado a retenido, se descuenta de nuevo la unidad.

---

## 5. Patrones de Integración HTMX y Alpine.js

### HTMX (Navegación SPA y Swaps Parciales)
- La función `render($template, $data)` detecta automáticamente si la cabecera `HTTP_HX_REQUEST` está presente:
  - Si es petición HTMX: renderiza únicamente la plantilla parcial sin el layout general.
  - Si es petición completa del navegador: renderiza la vista dentro de `templates/layouts/app.php`.
- Navegación global: los enlaces utilizan `hx-get="..." hx-target="#main-content" hx-push-url="true"` para mantener la URL actualizada en el historial del navegador.
- Búsqueda en vivo:
  ```html
  <input type="text" name="search" hx-get="/catalog" hx-target="#main-content" hx-trigger="keyup changed delay:400ms">
  ```
- Disparo de eventos del servidor hacia el cliente:
  ```php
  header('HX-Trigger: {"showToast": {"message": "Libro añadido al carrito", "type": "success"}}');
  ```

### Alpine.js (Micro-interacciones y UI Local)
- Los Toasts de notificación se gestionan reactivamente escuchando el evento global `@show-toast.window="addToast($event.detail.message, $event.detail.type)"`.
- El contador del badge del carrito se actualiza escuchando el evento `cartUpdated`.
- Botones de copiado de códigos en `checkout_result.php` utilizan la API de portapapeles con cambio de estado temporal (`navigator.clipboard.writeText(...)`).
- Los formularios con estado de procesamiento usan variables reactivas como `x-data="{ loading: false }"` para deshabilitar botones y mostrar spinners.

---

## 6. Seguridad y Buenas Prácticas

1. **Protección XSS:** Usar siempre `<?= e($variable) ?>` para imprimir variables en el HTML.
2. **Protección CSRF:**
   - Todos los formularios POST deben incluir `<?= csrf_field() ?>`.
   - Todo controlador que reciba POST debe invocar `AuthMiddleware::verifyCsrf()`.
3. **Inyecciones SQL:**
   - Queda terminantemente prohibido concatenar variables en sentencias SQL.
   - Utilizar sentencias preparadas con PDO (`$stmt->prepare()` y `$stmt->execute([...])`).
4. **Manejo de Errores y Mensajes Flash:**
   - Usar `flash('success', 'Mensaje')` o `flash('error', 'Mensaje')`.
   - Se recuperan en el layout automáticamente para renderizar alertas.

---

## 7. Comandos de Mantenimiento y Verificación

Cualquier agente que modifique código debe verificar la integridad del proyecto mediante los siguientes comandos en la terminal:

```bash
# 1. Verificar sintaxis PHP en todo el proyecto
find src/ templates/ -name "*.php" -exec php -l {} \;

# 2. Reiniciar la base de datos a un estado limpio con datos semilla
php database/init.php

# 3. Compilar la hoja de estilos de Tailwind CSS
./tailwindcss -i tailwind/input.css -o public/css/app.css --minify

# 4. Ejecutar la suite de pruebas de integración automatizadas
php test_integration.php
```

---

## 8. Consideraciones Especiales para Agentes
- **No introducir frameworks pesados:** No instales Laravel, Symfony, ni ORMs pesados. La naturaleza del proyecto es monolítica vanilla y debe mantenerse ligera y transparente.
- **Mantener la coherencia estética:** Respeta la paleta neón, morada y cian, asegurando que las clases de Tailwind utilicen `bg-[#0a0a0f]`, `border-purple-500/20`, acentos `text-cyan-400` y tipografías adecuadas.
- **Atención a SQLite en pruebas:** Si realizas consultas en scripts de prueba externos a la base de datos SQLite en modo WAL, genera conexiones frescas para evitar lectura de snapshots desactualizados.
