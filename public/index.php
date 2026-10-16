<?php
/**
 * HackLib Panel - Front Controller
 * Punto de entrada principal de la aplicación
 */

require_once __DIR__ . '/../vendor/autoload.php';

// Start session
session_start();

// Load config
$config = require __DIR__ . '/../config/app.php';

if ($config['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

use App\Router;
use App\Controllers\AuthController;
use App\Controllers\CatalogController;
use App\Controllers\AdminController;

$router = new Router();

// === PUBLIC ROUTES ===
$router->get('/', function() {
    if (is_authenticated()) {
        if (is_admin()) redirect('/admin');
        redirect('/catalog');
    }
    redirect('/login');
});

// Auth routes
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);

// User/Catalog routes
$router->get('/catalog', [CatalogController::class, 'index']);
$router->get('/catalog/{id}', [CatalogController::class, 'show']);
$router->post('/cart/add', [CatalogController::class, 'addToCart']);
$router->get('/cart', [CatalogController::class, 'viewCart']);
$router->post('/cart/remove', [CatalogController::class, 'removeFromCart']);
$router->post('/checkout', [CatalogController::class, 'checkout']);
$router->get('/my-rentals', [CatalogController::class, 'myRentals']);

// Admin routes
$router->get('/admin', [AdminController::class, 'dashboard']);

// Admin - Books
$router->get('/admin/books', [AdminController::class, 'books']);
$router->get('/admin/books/create', [AdminController::class, 'createBook']);
$router->post('/admin/books/store', [AdminController::class, 'storeBook']);
$router->get('/admin/books/edit/{id}', [AdminController::class, 'editBook']);
$router->post('/admin/books/update/{id}', [AdminController::class, 'updateBook']);
$router->post('/admin/books/delete/{id}', [AdminController::class, 'deleteBook']);

// Admin - Rentals
$router->get('/admin/rentals', [AdminController::class, 'rentals']);
$router->post('/admin/rentals/status/{id}', [AdminController::class, 'updateRentalStatus']);

// Admin - Users (Master Admin only)
$router->get('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/users/create', [AdminController::class, 'createUser']);
$router->post('/admin/users/store', [AdminController::class, 'storeUser']);
$router->get('/admin/users/edit/{id}', [AdminController::class, 'editUser']);
$router->post('/admin/users/update/{id}', [AdminController::class, 'updateUser']);
$router->post('/admin/users/delete/{id}', [AdminController::class, 'deleteUser']);

// Resolve the route
$router->resolve();
