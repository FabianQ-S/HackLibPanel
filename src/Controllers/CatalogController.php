<?php

namespace App\Controllers;

use App\Models\Book;
use App\Models\Rental;
use App\Middleware\AuthMiddleware;

class CatalogController
{
    private Book $bookModel;

    public function __construct()
    {
        $this->bookModel = new Book();
    }

    public function index(): void
    {
        AuthMiddleware::requireAuth();

        $filters = [
            'search' => $_GET['search'] ?? '',
            'categoria' => $_GET['categoria'] ?? '',
            'nivel' => $_GET['nivel'] ?? '',
            'formato' => $_GET['formato'] ?? '',
        ];

        $books = $this->bookModel->getAll($filters);
        $total = $this->bookModel->count($filters);

        render('user/catalog', [
            'title' => 'Catálogo de Libros',
            'books' => $books,
            'total' => $total,
            'filters' => $filters,
        ]);
    }

    public function show(string $id): void
    {
        AuthMiddleware::requireAuth();

        $book = $this->bookModel->findById((int)$id);
        if (!$book) {
            flash('error', 'Libro no encontrado.');
            redirect('/catalog');
        }

        render('user/book_detail', [
            'title' => $book['titulo'],
            'book' => $book,
        ]);
    }

    // Cart methods (session-based)
    public function addToCart(): void
    {
        AuthMiddleware::requireAuth();
        AuthMiddleware::verifyCsrf();

        $bookId = (int)($_POST['book_id'] ?? 0);
        $type = $_POST['type'] ?? 'compra'; // 'compra' or 'alquiler'

        $book = $this->bookModel->findById($bookId);
        if (!$book) {
            if (is_htmx()) {
                echo '<div class="text-red-400">Libro no encontrado.</div>';
                return;
            }
            flash('error', 'Libro no encontrado.');
            redirect('/catalog');
        }

        if ($book['stock'] <= 0) {
            if (is_htmx()) {
                echo '<div class="text-red-400">Sin stock disponible.</div>';
                return;
            }
            flash('error', 'Sin stock disponible.');
            redirect('/catalog');
        }

        // Initialize cart if needed
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if already in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['book_id'] === $bookId && $item['type'] === $type) {
                $item['quantity']++;
                $found = true;
                break;
            }
        }
        unset($item);

        if (!$found) {
            $_SESSION['cart'][] = [
                'book_id' => $bookId,
                'titulo' => $book['titulo'],
                'autor' => $book['autor'],
                'precio' => $book['precio'],
                'type' => $type,
                'quantity' => 1,
            ];
        }

        if (is_htmx()) {
            $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
            // Return updated cart count badge and success toast
            header('HX-Trigger: {"showToast": {"message": "Libro añadido al carrito", "type": "success"}, "cartUpdated": {"count": ' . $cartCount . '}}');
            echo '<span class="cart-count">' . $cartCount . '</span>';
            return;
        }

        flash('success', 'Libro añadido al carrito.');
        redirect('/catalog');
    }

    public function viewCart(): void
    {
        AuthMiddleware::requireAuth();

        $cart = $_SESSION['cart'] ?? [];
        $total = 0;
        foreach ($cart as $item) {
            if ($item['type'] === 'compra') {
                $total += $item['precio'] * $item['quantity'];
            }
        }

        render('user/cart', [
            'title' => 'Mi Carrito',
            'cart' => $cart,
            'total' => $total,
        ]);
    }

    public function removeFromCart(): void
    {
        AuthMiddleware::requireAuth();
        AuthMiddleware::verifyCsrf();

        $index = (int)($_POST['index'] ?? -1);

        if (isset($_SESSION['cart'][$index])) {
            array_splice($_SESSION['cart'], $index, 1);
        }

        if (is_htmx()) {
            // Re-render cart
            $cart = $_SESSION['cart'] ?? [];
            $total = 0;
            foreach ($cart as $item) {
                if ($item['type'] === 'compra') {
                    $total += $item['precio'] * $item['quantity'];
                }
            }
            $cartCount = array_sum(array_column($_SESSION['cart'] ?? [], 'quantity'));
            header('HX-Trigger: {"cartUpdated": {"count": ' . $cartCount . '}}');
            view('partials/cart_items', ['cart' => $cart, 'total' => $total]);
            return;
        }

        redirect('/cart');
    }

    public function checkout(): void
    {
        AuthMiddleware::requireAuth();
        AuthMiddleware::verifyCsrf();

        $cart = $_SESSION['cart'] ?? [];
        if (empty($cart)) {
            flash('error', 'Tu carrito está vacío.');
            redirect('/cart');
        }

        $rentalModel = new Rental();
        $user = auth();
        $rentalCodes = [];
        $purchasedBooks = [];

        foreach ($cart as $item) {
            if ($item['type'] === 'alquiler') {
                // Process rental - actually modifies DB
                for ($i = 0; $i < $item['quantity']; $i++) {
                    $rental = $rentalModel->create($user['id'], $item['book_id']);
                    if ($rental) {
                        $rentalCodes[] = [
                            'codigo' => $rental['codigo'],
                            'libro' => $item['titulo'],
                        ];
                    }
                }
            } else {
                // Simulated purchase - no DB modification
                $purchasedBooks[] = $item;
            }
        }

        // Clear cart
        $_SESSION['cart'] = [];

        render('user/checkout_result', [
            'title' => 'Resultado de la Compra',
            'rentalCodes' => $rentalCodes,
            'purchasedBooks' => $purchasedBooks,
        ]);
    }

    public function myRentals(): void
    {
        AuthMiddleware::requireAuth();

        $rentalModel = new Rental();
        $rentals = $rentalModel->getByUser(auth()['id']);

        render('user/my_rentals', [
            'title' => 'Mis Préstamos',
            'rentals' => $rentals,
        ]);
    }
}
