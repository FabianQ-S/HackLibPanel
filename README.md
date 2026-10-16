# HackLib Panel — Panel de Control para Librería de Programación

**HackLib Panel** es una plataforma web monolítica diseñada específicamente para la gestión, adquisición y préstamo de literatura técnica en ciencias de la computación, desarrollo de software e ingeniería de sistemas. La plataforma cuenta con una experiencia de usuario inmersiva con temática **Cyberpunk / Hacker Moderno**, caracterizada por interfaces oscuras, iluminación neón morada y cian, tipografías monoespaciadas y dinamismo propio de una aplicación de una sola página (SPA).

---

## Stack Tecnológico y Dependencias

El proyecto se diseñó bajo una filosofía monolítica pura y minimalista, evitando el sobrepeso de frameworks backend pesados como Laravel o Symfony, y eliminando la necesidad de empaquetadores complejos de JavaScript:

* **PHP 8.1+ Vanilla:** Actúa como el núcleo del servidor. Procesa el enrutamiento HTTP, ejecuta los controladores de negocio, gestiona las sesiones de usuario en memoria del servidor y ensambla las vistas de forma monolítica tradicional mediante funciones utilitarias limpias.
* **Composer:** Herramienta de gestión de paquetes y estándares. Se encarga exclusivamente de resolver el estándar de autocarga PSR-4 para las clases del proyecto y de cargar funciones globales auxiliares.
* **SQLite 3 (vía PHP PDO):** Motor de base de datos embebido en disco. Trabaja en modo WAL (Write-Ahead Logging) y con integridad referencial activada mediante llaves foráneas. Ofrece transacciones ACID seguras para el control estricto del inventario sin requerir servicios de bases de datos independientes.
* **HTMX:** Biblioteca ligera que dota a la aplicación del comportamiento fluido y reactivo de una Single Page Application (SPA). Permite ejecutar búsquedas en vivo, añadir o eliminar libros del carrito y cambiar estados administrativos mediante peticiones asíncronas que intercambian fragmentos de HTML directamente en el navegador sin recargas de página.
* **Alpine.js:** Framework declarativo y ligero de JavaScript para micro-interacciones en el cliente. Administra las notificaciones emergentes (toasts), el copiado interactivo de códigos al portapapeles, los estados de carga en botones y la apertura o cierre de paneles laterales móviles.
* **Tailwind CSS (CLI Standalone):** Motor de diseño utilitario compilado localmente a través de su binario oficial independiente para Linux x64. Genera una hoja de estilos optimizada y minificada basada en una paleta personalizada de alta fidelidad hacker/cyberpunk.

---

## Lógica de Negocio y Funcionalidades al Detalle

### 1. Sistema de Autenticación y Matriz de Roles

La autenticación se basa en sesiones nativas seguras en el servidor, implementando un control de acceso jerárquico de tres niveles:

#### Super Administrador Maestro (Master Admin)
* Posee privilegios absolutos sobre el sistema.
* Tiene acceso total a las estadísticas del panel de control, al inventario de libros y a la gestión del ciclo de vida de los préstamos.
* **Privilegio exclusivo:** Es el único usuario facultado para acceder a la gestión de personal, permitiéndole registrar nuevos administradores y administrar a los usuarios clientes.
* Esta cuenta maestra es indestructible dentro del sistema: no puede ser eliminada ni rebajada de rol.

#### Administrador Operativo (Admin de Negocio)
* Perfil creado exclusivamente por el Super Administrador Maestro.
* Diseñado para el personal de mostrador y bibliotecarios operativos.
* Puede registrar nuevos libros, editar información bibliográfica, actualizar existencias y modificar el estado de los préstamos de los usuarios.
* **Restricción de seguridad:** Por principio de mínimo privilegio, tiene vedado el acceso al módulo de usuarios y no puede crear ni alterar permisos de otros administradores.

#### Usuario Registrado (Cliente)
* Cuenta obtenida mediante el formulario de registro público.
* Puede explorar el catálogo de obras disponibles, consultar fichas técnicas detalladas y realizar búsquedas por filtros avanzados.
* Tiene acceso a su propio carrito de compras, puede solicitar reservas de préstamo físico y monitorear el estado de sus solicitudes desde su historial personal.

---

### 2. Catálogo Especializado y Atributos Técnicos

El inventario bibliográfico está segmentado en tres categorías base que responden a la realidad formativa de la ingeniería de software:

* **Lenguajes:** Textos concentrados exclusivamente en la sintaxis, semántica, tipado y paradigmas puros de lenguajes de programación (por ejemplo: *The Rust Programming Language*, *Programming Ruby*, *Learn You Some Erlang*).
* **Aprendizaje / Fundamentos:** Teoría formal, conceptos agnósticos y principios aplicables a cualquier tecnología (por ejemplo: Algoritmos y Estructuras de Datos, Patrones de Diseño, Clean Code, Arquitectura de Sistemas Distribuidos y Entornos Linux/POSIX).
* **Frameworks & Herramientas:** Ecosistemas de desarrollo, librerías de producción y herramientas de infraestructura (por ejemplo: Rails, Laravel, Phoenix, Docker, Kubernetes, Git).

Cada obra incluye campos técnicos rigurosos:
* **Nivel de Complejidad:** Principiante, Intermedio o Avanzado.
* **Versión de la Tecnología:** Versión específica que cubre el libro (por ejemplo, Python 3.12, Rails 8, PHP 8.3), aspecto crítico en computación donde un texto desactualizado pierde vigencia.
* **Formato:** Tapa Blanda, Tapa Dura o Libro Electrónico (eBook / PDF).
* **Parámetros Comerciales:** Cantidad de unidades disponibles en almacén (stock), precio unitario, código ISBN normalizado y año de edición.

El catálogo ofrece una barra de búsqueda en tiempo real con temporizador de espera (*debounce*), permitiendo filtrar simultáneamente por texto, categoría temática, nivel de dificultad y formato físico sin recargar la pantalla.

---

### 3. Carrito de Operaciones Dual (Compra Simulada vs. Alquiler Real)

El carrito de compras permite alternar entre dos naturalezas operativas completamente diferenciadas:

#### A. Modalidad Compra (Simulación Comercial)
* Diseñada para usuarios interesados en la adquisición definitiva de libros.
* Es una **simulación didáctica**: calcula los subtotales y totales monetarios, pero al momento de confirmar el pedido, **no descuenta inventario ni altera la base de datos**.
* Al finalizar el proceso, la plataforma presenta un informe detallado con un mensaje explícito de compra completada con éxito.

#### B. Modalidad Alquiler / Préstamo (Gestión de Inventario Físico)
* Diseñada para la reserva física de libros en la biblioteca.
* Es una **operación real con impacto transaccional**: al confirmar la solicitud, el sistema descuenta de inmediato **una unidad (-1)** de las existencias disponibles del libro para garantizar la reserva.
* Si un libro tiene existencia cero, la plataforma bloquea automáticamente su solicitud de préstamo.
* El sistema genera un código aleatorio criptográfico único para cada ejemplar solicitado (con formato alfanumérico identificable).
* El usuario recibe una advertencia formal con instrucciones precisas: debe acudir al punto físico de entrega y presentar dicho código ante el bibliotecario.
* Cada usuario puede consultar permanentemente sus códigos activos y el estado de cada solicitud desde su sección personal.

---

### 4. Ciclo de Vida y Transición de Estados del Préstamo

En la consola administrativa, el personal puede inspeccionar todas las reservas entrantes mediante su código único. El ciclo de vida de un préstamo transcurre por los siguientes estados:

* **Pendiente:** Estado inicial asignado al generar la reserva. El libro ya se encuentra apartado y el stock ha sido reducido.
* **Aprobado:** El administrador valida que el ejemplar físico está preparado en el mostrador para su entrega.
* **Prestado:** El usuario se presenta físicamente, entrega su código de retiro, el administrador verifica la orden y entrega el ejemplar.
* **Devuelto:** El usuario entrega el libro de regreso en la biblioteca. **En este instante, el sistema restaura de forma automática e inmediata la existencia del libro (+1)** en la base de datos, dejándolo disponible nuevamente para otros lectores.
* **Cancelado:** Si el usuario no acude a retirar su solicitud o se anula la reserva, el sistema restituye la unidad de stock al catálogo.

---

### 5. Experiencia de Usuario y Estética Cyberpunk

La interfaz fue diseñada bajo lineamientos de diseño contemporáneo inspirado en herramientas ofensivas y defensivas de ciberseguridad:
* Paleta visual dominada por fondos ultra oscuros, bordes con resplandor neón violeta y destacados lumínicos en cian.
* Efectos visuales de texto distorsionado (*glitch text*), cursores parpadeantes simulando una sesión activa de terminal y líneas de barrido (*scanlines*).
* Notificaciones tipo *Toast* animadas en la esquina superior que confirman acciones del servidor en milisegundos.
* Navegación instantánea mediante intercambio parcial del DOM sin saltos de página ni pérdidas del estado de la interfaz.

---

## Puesta en Marcha y Credenciales

### Requisitos Mínimos
* Sistema operativo Linux (optimizado con script nativo para Fedora Linux).
* PHP versión 8.1 o superior con extensiones SQLite y PDO habilitadas.
* Administrador de paquetes Composer.

### Credenciales Maestras Preconfiguradas
* **Usuario:** `admin`
* **Contraseña:** `admin123`
* **Rol:** Super Administrador Maestro

Los usuarios regulares pueden ser creados libremente desde la pantalla de registro público de la aplicación.
