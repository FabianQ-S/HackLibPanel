<?php
/**
 * Funciones helper globales
 */

/**
 * Escapa HTML para prevenir XSS
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

/**
 * Genera un token CSRF y lo almacena en sesión
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Genera un campo hidden con el token CSRF
 */
function csrf_field(): string
{
    return '<input type="hidden" name="_csrf_token" value="' . csrf_token() . '">';
}

/**
 * Verifica el token CSRF
 */
function verify_csrf(string $token): bool
{
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Redirige a una URL
 */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/**
 * Establece un mensaje flash en sesión
 */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][$type] = $message;
}

/**
 * Obtiene y elimina un mensaje flash
 */
function get_flash(string $type): ?string
{
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

/**
 * Verifica si hay un mensaje flash
 */
function has_flash(string $type): bool
{
    return isset($_SESSION['flash'][$type]);
}

/**
 * Obtiene el usuario autenticado actual
 */
function auth(): ?array
{
    return $_SESSION['user'] ?? null;
}

/**
 * Verifica si el usuario está autenticado
 */
function is_authenticated(): bool
{
    return isset($_SESSION['user']);
}

/**
 * Verifica si el usuario es admin (master o regular)
 */
function is_admin(): bool
{
    $user = auth();
    return $user && in_array($user['role'], ['master_admin', 'admin']);
}

/**
 * Verifica si el usuario es master admin
 */
function is_master_admin(): bool
{
    $user = auth();
    return $user && $user['role'] === 'master_admin';
}

/**
 * Genera un código aleatorio para préstamos
 */
function generate_rental_code(): string
{
    return 'RENT-' . strtoupper(bin2hex(random_bytes(4))) . '-' . date('ymd');
}

/**
 * Formatea precio
 */
function format_price(float $price): string
{
    return '$' . number_format($price, 2);
}

/**
 * Verifica si la petición es HTMX
 */
function is_htmx(): bool
{
    return isset($_SERVER['HTTP_HX_REQUEST']);
}

/**
 * Obtiene la URI actual
 */
function current_uri(): string
{
    return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}

/**
 * Renderiza una vista
 */
function view(string $template, array $data = []): void
{
    extract($data);
    $templatePath = __DIR__ . '/../../templates/' . $template . '.php';
    if (!file_exists($templatePath)) {
        http_response_code(404);
        echo "Template not found: {$template}";
        return;
    }
    require $templatePath;
}

/**
 * Renderiza una vista dentro del layout
 */
function render(string $template, array $data = [], string $layout = 'layouts/app'): void
{
    // If HTMX request, render without layout
    if (is_htmx() && !isset($data['force_layout'])) {
        view($template, $data);
        return;
    }
    
    // Capture the content
    ob_start();
    view($template, $data);
    $content = ob_get_clean();
    
    $data['content'] = $content;
    view($layout, $data);
}

/**
 * Traduce categoría a texto legible
 */
function categoria_label(string $cat): string
{
    return match($cat) {
        'lenguajes' => 'Lenguajes',
        'fundamentos' => 'Aprendizaje / Fundamentos',
        'frameworks' => 'Frameworks & Herramientas',
        default => ucfirst($cat),
    };
}

/**
 * Traduce nivel a texto legible
 */
function nivel_label(string $nivel): string
{
    return match($nivel) {
        'principiante' => 'Principiante',
        'intermedio' => 'Intermedio',
        'avanzado' => 'Avanzado',
        default => ucfirst($nivel),
    };
}

/**
 * Traduce formato a texto legible
 */
function formato_label(string $formato): string
{
    return match($formato) {
        'tapa_blanda' => 'Tapa Blanda',
        'tapa_dura' => 'Tapa Dura',
        'ebook' => 'eBook / PDF',
        default => ucfirst($formato),
    };
}

/**
 * Traduce estado de rental a texto legible
 */
function rental_estado_label(string $estado): string
{
    return match($estado) {
        'pendiente' => 'Pendiente',
        'aprobado' => 'Aprobado',
        'prestado' => 'Prestado',
        'devuelto' => 'Devuelto',
        'cancelado' => 'Cancelado',
        default => ucfirst($estado),
    };
}

/**
 * Color badge para nivel
 */
function nivel_color(string $nivel): string
{
    return match($nivel) {
        'principiante' => 'bg-green-500/20 text-green-400 border-green-500/30',
        'intermedio' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
        'avanzado' => 'bg-red-500/20 text-red-400 border-red-500/30',
        default => 'bg-gray-500/20 text-gray-400 border-gray-500/30',
    };
}

/**
 * Color badge para categoría
 */
function categoria_color(string $cat): string
{
    return match($cat) {
        'lenguajes' => 'bg-cyan-500/20 text-cyan-400 border-cyan-500/30',
        'fundamentos' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
        'frameworks' => 'bg-pink-500/20 text-pink-400 border-pink-500/30',
        default => 'bg-gray-500/20 text-gray-400 border-gray-500/30',
    };
}

/**
 * Color badge para estado rental
 */
function rental_estado_color(string $estado): string
{
    return match($estado) {
        'pendiente' => 'bg-yellow-500/20 text-yellow-400 border-yellow-500/30',
        'aprobado' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
        'prestado' => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
        'devuelto' => 'bg-green-500/20 text-green-400 border-green-500/30',
        'cancelado' => 'bg-red-500/20 text-red-400 border-red-500/30',
        default => 'bg-gray-500/20 text-gray-400 border-gray-500/30',
    };
}
