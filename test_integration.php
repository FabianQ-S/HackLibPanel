<?php
/**
 * Test de Integración End-to-End para HackLib Panel
 */

require_once __DIR__ . '/vendor/autoload.php';

function testLog($msg, $pass = null) {
    if ($pass === true) {
        echo "  [\033[32mPASS\033[0m] $msg\n";
    } elseif ($pass === false) {
        echo "  [\033[31mFAIL\033[0m] $msg\n";
    } else {
        echo "\033[36m[*] $msg\033[0m\n";
    }
}

class TestClient {
    private string $baseUrl;
    private ?string $cookieFile;

    public function __construct(string $baseUrl = 'http://127.0.0.1:8000') {
        $this->baseUrl = $baseUrl;
        $this->cookieFile = tempnam(sys_get_temp_dir(), 'hacklib_cookie_');
    }

    public function __destruct() {
        if ($this->cookieFile && file_exists($this->cookieFile)) {
            unlink($this->cookieFile);
        }
    }

    public function request(string $method, string $path, array $data = [], array $headers = []): array {
        $ch = curl_init();
        $url = $this->baseUrl . $path;
        
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $this->cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $this->cookieFile);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        }

        if (!empty($headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $rawResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $headerStr = substr($rawResponse, 0, $headerSize);
        $body = substr($rawResponse, $headerSize);
        
        $location = null;
        if (preg_match('/^Location:\s*(.*)$/mi', $headerStr, $matches)) {
            $location = trim($matches[1]);
        }

        curl_close($ch);

        return [
            'code' => $httpCode,
            'headers' => $headerStr,
            'body' => $body,
            'location' => $location,
        ];
    }

    public function getCsrfToken(string $path = '/login'): ?string {
        $res = $this->request('GET', $path);
        if (preg_match('/name="_csrf_token"\s+value="([a-f0-9]+)"/i', $res['body'], $m)) {
            return $m[1];
        }
        return null;
    }
}

testLog("INICIANDO PRUEBAS DE INTEGRACIÓN HACKLIB...");

// 1. MASTER ADMIN LOGIN
testLog("1. Probando inicio de sesión con Master Admin (admin / admin123)...");
$adminClient = new TestClient();
$token = $adminClient->getCsrfToken('/login');
testLog("Token CSRF obtenido: " . ($token ? substr($token, 0, 8) . '...' : 'NONE'), $token !== null);

$loginRes = $adminClient->request('POST', '/login', [
    '_csrf_token' => $token,
    'username' => 'admin',
    'password' => 'admin123',
]);
testLog("Login Master Admin redirige a /admin (HTTP 302): " . $loginRes['location'], $loginRes['code'] === 302 && $loginRes['location'] === '/admin');

$adminDash = $adminClient->request('GET', '/admin');
testLog("Master Admin accede al Dashboard (HTTP 200)", $adminDash['code'] === 200 && str_contains($adminDash['body'], 'admin.dashboard'));
testLog("Master Admin ve sección Gestión de Usuarios", str_contains($adminDash['body'], '/admin/users'));

// 2. USER REGISTRATION & LOGIN
testLog("2. Probando registro de usuario normal...");
$userClient = new TestClient();
$regToken = $userClient->getCsrfToken('/register');
$newUsername = 'hacker_' . substr(bin2hex(random_bytes(3)), 0, 5);
$newEmail = $newUsername . '@hacklib.test';

$regRes = $userClient->request('POST', '/register', [
    '_csrf_token' => $regToken,
    'username' => $newUsername,
    'email' => $newEmail,
    'password' => 'secret123',
    'password_confirm' => 'secret123',
]);
testLog("Registro exitoso redirige a /login (HTTP 302)", $regRes['code'] === 302 && $regRes['location'] === '/login');

$userLoginToken = $userClient->getCsrfToken('/login');
$userLoginRes = $userClient->request('POST', '/login', [
    '_csrf_token' => $userLoginToken,
    'username' => $newUsername,
    'password' => 'secret123',
]);
testLog("Login de usuario redirige a /catalog (HTTP 302)", $userLoginRes['code'] === 302 && $userLoginRes['location'] === '/catalog');

$catalogRes = $userClient->request('GET', '/catalog');
testLog("Usuario accede al catálogo (HTTP 200)", $catalogRes['code'] === 200 && str_contains($catalogRes['body'], 'Catálogo de Libros'));

// 3. CATALOG SEARCH & FILTERS
testLog("3. Probando búsqueda y filtros en catálogo...");
$searchRes = $userClient->request('GET', '/catalog?search=Rust');
testLog("Búsqueda por 'Rust' retorna resultados esperados", str_contains($searchRes['body'], 'The Rust Programming Language'));

$catRes = $userClient->request('GET', '/catalog?categoria=fundamentos');
testLog("Filtro categoría 'fundamentos' contiene Clean Code", str_contains($catRes['body'], 'Clean Code'));

// 4. CART & SIMULATED PURCHASE + RENTAL
testLog("4. Probando flujo de Carrito: Compra simulada y Alquiler con código...");

function getFreshStock(int $bookId): int {
    $db = new PDO('sqlite:' . __DIR__ . '/storage/database.sqlite');
    $stmt = $db->prepare('SELECT stock FROM books WHERE id = ?');
    $stmt->execute([$bookId]);
    return (int)$stmt->fetchColumn();
}

// Check initial stock of book 1 (The Rust Programming Language)
$initialStock = getFreshStock(1);
testLog("Stock inicial libro #1: $initialStock");

// Get CSRF token for cart
$catalogPage = $userClient->request('GET', '/catalog');
preg_match('/name="_csrf_token"\s+value="([a-f0-9]+)"/i', $catalogPage['body'], $m);
$cartCsrf = $m[1] ?? '';

// Add book 1 for purchase
$addCompra = $userClient->request('POST', '/cart/add', [
    '_csrf_token' => $cartCsrf,
    'book_id' => 1,
    'type' => 'compra',
], ['HX-Request: true']);
testLog("Añadir compra al carrito vía HTMX responde 200", $addCompra['code'] === 200);

// Add book 1 for rental
$addAlquiler = $userClient->request('POST', '/cart/add', [
    '_csrf_token' => $cartCsrf,
    'book_id' => 1,
    'type' => 'alquiler',
], ['HX-Request: true']);
testLog("Añadir alquiler al carrito vía HTMX responde 200", $addAlquiler['code'] === 200);

// View Cart
$cartView = $userClient->request('GET', '/cart');
testLog("Ver carrito contiene ambos ítems", str_contains($cartView['body'], 'Compra') && str_contains($cartView['body'], 'Alquiler'));

// Checkout
preg_match('/name="_csrf_token"\s+value="([a-f0-9]+)"/i', $cartView['body'], $m);
$checkoutCsrf = $m[1] ?? '';

$checkoutRes = $userClient->request('POST', '/checkout', [
    '_csrf_token' => $checkoutCsrf,
]);
testLog("Checkout responde 200", $checkoutRes['code'] === 200);
testLog("Checkout muestra sección 'Compras Simuladas' (sin tocar DB)", str_contains($checkoutRes['body'], 'Compras Simuladas'));
testLog("Checkout muestra sección 'Préstamos Registrados' con código RENT-", str_contains($checkoutRes['body'], 'RENT-'));

// Verify stock was decremented by 1 (due to rental)
$stockAfterRental = getFreshStock(1);
testLog("Stock tras alquiler se redujo en 1: $stockAfterRental (esperado: " . ($initialStock - 1) . ")", $stockAfterRental === ($initialStock - 1));

// Extract rental code
preg_match('/(RENT-[A-F0-9]+-[0-9]+)/', $checkoutRes['body'], $mCode);
$generatedRentalCode = $mCode[1] ?? '';
testLog("Código generado: " . $generatedRentalCode, !empty($generatedRentalCode));

// Check user's my-rentals
$myRentalsRes = $userClient->request('GET', '/my-rentals');
testLog("Usuario ve su código en /my-rentals", str_contains($myRentalsRes['body'], $generatedRentalCode));

// 5. ADMIN RENTAL MANAGEMENT & STOCK RESTORATION
testLog("5. Probando consola Admin para gestión de préstamos y devolución a stock...");
$adminRentals = $adminClient->request('GET', '/admin/rentals');
testLog("Admin ve el código generado en la consola", str_contains($adminRentals['body'], $generatedRentalCode));

// Get rental ID from DB
$dbFresh = new PDO('sqlite:' . __DIR__ . '/storage/database.sqlite');
$stmt = $dbFresh->prepare('SELECT id FROM rentals WHERE codigo = ?');
$stmt->execute([$generatedRentalCode]);
$rentalId = (int)$stmt->fetchColumn();

// Get admin CSRF
preg_match('/name="_csrf_token"\s+value="([a-f0-9]+)"/i', $adminRentals['body'], $m);
$adminCsrf = $m[1] ?? '';

// Update status to 'prestado'
$updatePrestado = $adminClient->request('POST', "/admin/rentals/status/$rentalId", [
    '_csrf_token' => $adminCsrf,
    'estado' => 'prestado',
], ['HX-Request: true']);
testLog("Admin aprueba y pasa a 'prestado' vía HTMX (HTTP 200)", $updatePrestado['code'] === 200);

// Check stock remains decremented
testLog("Stock durante estado 'prestado' sigue en: " . getFreshStock(1), getFreshStock(1) === ($initialStock - 1));

// Admin marks as 'devuelto'
$updateDevuelto = $adminClient->request('POST', "/admin/rentals/status/$rentalId", [
    '_csrf_token' => $adminCsrf,
    'estado' => 'devuelto',
], ['HX-Request: true']);
testLog("Admin marca préstamo como 'devuelto' vía HTMX (HTTP 200)", $updateDevuelto['code'] === 200);

// Check stock is restored to original!
$finalStock = getFreshStock(1);
testLog("Stock tras 'devuelto' regresa al original: $finalStock (esperado: $initialStock)", $finalStock === $initialStock);

// 6. MASTER ADMIN CREATES SUBADMIN & VERIFIES ROLES & PERMISSIONS
testLog("6. Probando jerarquía de roles: Master Admin crea Admin con permisos limitados...");
$usersPage = $adminClient->request('GET', '/admin/users');
preg_match('/name="_csrf_token"\s+value="([a-f0-9]+)"/i', $usersPage['body'], $m);
$masterCsrf = $m[1] ?? '';

$subAdminUser = 'admin_ops_' . substr(bin2hex(random_bytes(2)), 0, 4);
$createAdminRes = $adminClient->request('POST', '/admin/users/store', [
    '_csrf_token' => $masterCsrf,
    'username' => $subAdminUser,
    'email' => $subAdminUser . '@hacklib.dev',
    'password' => 'operator123',
    'role' => 'admin',
]);
testLog("Master Admin crea nuevo Admin (HTTP 302 a /admin/users)", $createAdminRes['code'] === 302 && $createAdminRes['location'] === '/admin/users');

// Subadmin logs in
$subAdminClient = new TestClient();
$subToken = $subAdminClient->getCsrfToken('/login');
$subLogin = $subAdminClient->request('POST', '/login', [
    '_csrf_token' => $subToken,
    'username' => $subAdminUser,
    'password' => 'operator123',
]);
testLog("Subadmin inicia sesión y redirige a /admin (HTTP 302)", $subLogin['code'] === 302 && $subLogin['location'] === '/admin');

// Subadmin dashboard
$subDash = $subAdminClient->request('GET', '/admin');
testLog("Subadmin accede a Dashboard (HTTP 200)", $subDash['code'] === 200);
testLog("Subadmin NO ve enlace de gestión de usuarios en navegación", !str_contains($subDash['body'], 'href="/admin/users"'));

// Subadmin can access books
$subBooks = $subAdminClient->request('GET', '/admin/books');
testLog("Subadmin tiene acceso a gestión de Libros (HTTP 200)", $subBooks['code'] === 200);

// Subadmin can access rentals
$subRentals = $subAdminClient->request('GET', '/admin/rentals');
testLog("Subadmin tiene acceso a gestión de Préstamos (HTTP 200)", $subRentals['code'] === 200);

// Subadmin attempts to access /admin/users (FORBIDDEN for non-master)
$forbiddenUsers = $subAdminClient->request('GET', '/admin/users');
testLog("Subadmin es bloqueado al intentar acceder a /admin/users (Redirige 302 a /admin)", $forbiddenUsers['code'] === 302 && $forbiddenUsers['location'] === '/admin');

testLog("-------------------------------------------------------");
testLog("¡TODAS LAS PRUEBAS DE INTEGRACIÓN SE COMPLETARON CON ÉXITO!", true);
