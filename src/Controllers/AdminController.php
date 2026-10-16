<?php

namespace App\Controllers;

use App\Models\Book;
use App\Models\User;
use App\Models\Rental;
use App\Middleware\AuthMiddleware;

class AdminController
{
    private Book $bookModel;
    private User $userModel;
    private Rental $rentalModel;

    public function __construct()
    {
        $this->bookModel = new Book();
        $this->userModel = new User();
        $this->rentalModel = new Rental();
    }

    // Dashboard
    public function dashboard(): void
    {
        AuthMiddleware::requireAdmin();

        $stats = $this->bookModel->getStats();
        $stats['total_users'] = $this->userModel->count('user');
        $stats['total_admins'] = $this->userModel->count('admin');
        $stats['pending_rentals'] = $this->rentalModel->getPendingCount();
        $stats['active_rentals'] = $this->rentalModel->getActiveCount();

        render('admin/dashboard', [
            'title' => 'Panel de Administración',
            'stats' => $stats,
        ]);
    }

    // === BOOKS MANAGEMENT ===
    public function books(): void
    {
        AuthMiddleware::requireAdmin();

        $filters = [
            'search' => $_GET['search'] ?? '',
            'categoria' => $_GET['categoria'] ?? '',
            'nivel' => $_GET['nivel'] ?? '',
        ];

        $books = $this->bookModel->getAll($filters);

        render('admin/books/index', [
            'title' => 'Gestión de Libros',
            'books' => $books,
            'filters' => $filters,
        ]);
    }

    public function createBook(): void
    {
        AuthMiddleware::requireAdmin();
        render('admin/books/create', ['title' => 'Nuevo Libro']);
    }

    public function storeBook(): void
    {
        AuthMiddleware::requireAdmin();
        AuthMiddleware::verifyCsrf();

        $data = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'autor' => trim($_POST['autor'] ?? ''),
            'categoria' => $_POST['categoria'] ?? '',
            'nivel' => $_POST['nivel'] ?? '',
            'version_tecnologia' => trim($_POST['version_tecnologia'] ?? ''),
            'formato' => $_POST['formato'] ?? '',
            'stock' => (int)($_POST['stock'] ?? 0),
            'precio' => (float)($_POST['precio'] ?? 0),
            'isbn' => trim($_POST['isbn'] ?? '') ?: null,
            'anio_publicacion' => (int)($_POST['anio_publicacion'] ?? date('Y')),
        ];

        // Basic validation
        if (empty($data['titulo']) || empty($data['autor']) || empty($data['categoria'])) {
            flash('error', 'Título, autor y categoría son obligatorios.');
            redirect('/admin/books/create');
        }

        $this->bookModel->create($data);
        flash('success', 'Libro creado exitosamente.');
        redirect('/admin/books');
    }

    public function editBook(string $id): void
    {
        AuthMiddleware::requireAdmin();

        $book = $this->bookModel->findById((int)$id);
        if (!$book) {
            flash('error', 'Libro no encontrado.');
            redirect('/admin/books');
        }

        render('admin/books/edit', [
            'title' => 'Editar Libro',
            'book' => $book,
        ]);
    }

    public function updateBook(string $id): void
    {
        AuthMiddleware::requireAdmin();
        AuthMiddleware::verifyCsrf();

        $book = $this->bookModel->findById((int)$id);
        if (!$book) {
            flash('error', 'Libro no encontrado.');
            redirect('/admin/books');
        }

        $data = [
            'titulo' => trim($_POST['titulo'] ?? ''),
            'autor' => trim($_POST['autor'] ?? ''),
            'categoria' => $_POST['categoria'] ?? '',
            'nivel' => $_POST['nivel'] ?? '',
            'version_tecnologia' => trim($_POST['version_tecnologia'] ?? ''),
            'formato' => $_POST['formato'] ?? '',
            'stock' => (int)($_POST['stock'] ?? 0),
            'precio' => (float)($_POST['precio'] ?? 0),
            'isbn' => trim($_POST['isbn'] ?? '') ?: null,
            'anio_publicacion' => (int)($_POST['anio_publicacion'] ?? date('Y')),
        ];

        $this->bookModel->update((int)$id, $data);
        flash('success', 'Libro actualizado exitosamente.');
        redirect('/admin/books');
    }

    public function deleteBook(string $id): void
    {
        AuthMiddleware::requireAdmin();
        AuthMiddleware::verifyCsrf();

        $this->bookModel->delete((int)$id);
        flash('success', 'Libro eliminado.');

        if (is_htmx()) {
            header('HX-Trigger: {"showToast": {"message": "Libro eliminado", "type": "success"}}');
            header('HX-Redirect: /admin/books');
            return;
        }

        redirect('/admin/books');
    }

    // === RENTALS MANAGEMENT ===
    public function rentals(): void
    {
        AuthMiddleware::requireAdmin();

        $filters = [
            'estado' => $_GET['estado'] ?? '',
            'search' => $_GET['search'] ?? '',
        ];

        $rentals = $this->rentalModel->getAll($filters);

        render('admin/rentals/index', [
            'title' => 'Gestión de Préstamos',
            'rentals' => $rentals,
            'filters' => $filters,
        ]);
    }

    public function updateRentalStatus(string $id): void
    {
        AuthMiddleware::requireAdmin();
        AuthMiddleware::verifyCsrf();

        $newStatus = $_POST['estado'] ?? '';
        $validStatuses = ['pendiente', 'aprobado', 'prestado', 'devuelto', 'cancelado'];

        if (!in_array($newStatus, $validStatuses)) {
            flash('error', 'Estado inválido.');
            redirect('/admin/rentals');
        }

        $this->rentalModel->updateStatus((int)$id, $newStatus);
        flash('success', 'Estado del préstamo actualizado.');

        if (is_htmx()) {
            header('HX-Trigger: {"showToast": {"message": "Estado del préstamo actualizado con éxito.", "type": "success"}}');
            $rentals = $this->rentalModel->getAll();
            view('partials/rental_rows', ['rentals' => $rentals]);
            return;
        }

        redirect('/admin/rentals');
    }

    // === USER MANAGEMENT (Master Admin only for admin creation) ===
    public function users(): void
    {
        AuthMiddleware::requireMasterAdmin();

        $users = $this->userModel->getAll();

        render('admin/users/index', [
            'title' => 'Gestión de Usuarios',
            'users' => $users,
        ]);
    }

    public function createUser(): void
    {
        AuthMiddleware::requireMasterAdmin();
        render('admin/users/create', ['title' => 'Nuevo Usuario/Admin']);
    }

    public function storeUser(): void
    {
        AuthMiddleware::requireMasterAdmin();
        AuthMiddleware::verifyCsrf();

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => $_POST['role'] ?? 'user',
        ];

        // Validation
        $errors = [];

        if (empty($data['username']) || strlen($data['username']) < 3) {
            $errors[] = 'El nombre de usuario debe tener al menos 3 caracteres.';
        }
        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email inválido.';
        }
        if (empty($data['password']) || strlen($data['password']) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }
        if (!in_array($data['role'], ['user', 'admin'])) {
            $errors[] = 'Rol inválido.';
        }
        if ($this->userModel->findByUsername($data['username'])) {
            $errors[] = 'El nombre de usuario ya existe.';
        }
        if ($this->userModel->findByEmail($data['email'])) {
            $errors[] = 'El email ya está registrado.';
        }

        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            redirect('/admin/users/create');
        }

        $this->userModel->create($data);
        flash('success', 'Usuario creado exitosamente.');
        redirect('/admin/users');
    }

    public function editUser(string $id): void
    {
        AuthMiddleware::requireMasterAdmin();

        $user = $this->userModel->findById((int)$id);
        if (!$user) {
            flash('error', 'Usuario no encontrado.');
            redirect('/admin/users');
        }

        render('admin/users/edit', [
            'title' => 'Editar Usuario',
            'editUser' => $user,
        ]);
    }

    public function updateUser(string $id): void
    {
        AuthMiddleware::requireMasterAdmin();
        AuthMiddleware::verifyCsrf();

        $user = $this->userModel->findById((int)$id);
        if (!$user || $user['role'] === 'master_admin') {
            flash('error', 'No se puede editar este usuario.');
            redirect('/admin/users');
        }

        $data = [
            'username' => trim($_POST['username'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'role' => $_POST['role'] ?? $user['role'],
        ];

        if (!empty($_POST['password'])) {
            $data['password'] = $_POST['password'];
        }

        $this->userModel->update((int)$id, $data);
        flash('success', 'Usuario actualizado exitosamente.');
        redirect('/admin/users');
    }

    public function deleteUser(string $id): void
    {
        AuthMiddleware::requireMasterAdmin();
        AuthMiddleware::verifyCsrf();

        $user = $this->userModel->findById((int)$id);
        if (!$user || $user['role'] === 'master_admin') {
            flash('error', 'No se puede eliminar este usuario.');
            redirect('/admin/users');
        }

        $this->userModel->delete((int)$id);
        flash('success', 'Usuario eliminado.');

        if (is_htmx()) {
            header('HX-Redirect: /admin/users');
            return;
        }

        redirect('/admin/users');
    }
}
