<?php

namespace App\Controllers;

use App\Models\User;
use App\Middleware\AuthMiddleware;

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function showLogin(): void
    {
        AuthMiddleware::redirectIfAuth();
        render('auth/login', ['title' => 'Iniciar Sesión']);
    }

    public function login(): void
    {
        AuthMiddleware::verifyCsrf();

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($username) || empty($password)) {
            flash('error', 'Todos los campos son obligatorios.');
            redirect('/login');
        }

        $user = $this->userModel->authenticate($username, $password);

        if (!$user) {
            flash('error', 'Credenciales inválidas.');
            redirect('/login');
        }

        // Set session
        $_SESSION['user'] = $user;
        session_regenerate_id(true);

        flash('success', '¡Bienvenido, ' . e($user['username']) . '!');

        if (in_array($user['role'], ['master_admin', 'admin'])) {
            redirect('/admin');
        }

        redirect('/catalog');
    }

    public function showRegister(): void
    {
        AuthMiddleware::redirectIfAuth();
        render('auth/register', ['title' => 'Registrarse']);
    }

    public function register(): void
    {
        AuthMiddleware::verifyCsrf();

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';

        // Validation
        $errors = [];

        if (empty($username) || strlen($username) < 3) {
            $errors[] = 'El nombre de usuario debe tener al menos 3 caracteres.';
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Introduce un email válido.';
        }

        if (empty($password) || strlen($password) < 6) {
            $errors[] = 'La contraseña debe tener al menos 6 caracteres.';
        }

        if ($password !== $passwordConfirm) {
            $errors[] = 'Las contraseñas no coinciden.';
        }

        if ($this->userModel->findByUsername($username)) {
            $errors[] = 'El nombre de usuario ya está en uso.';
        }

        if ($this->userModel->findByEmail($email)) {
            $errors[] = 'El email ya está registrado.';
        }

        if (!empty($errors)) {
            flash('error', implode('<br>', $errors));
            redirect('/register');
        }

        // Create user (always as 'user' role from public registration)
        $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'role' => 'user',
        ]);

        flash('success', '¡Registro exitoso! Ahora puedes iniciar sesión.');
        redirect('/login');
    }

    public function logout(): void
    {
        $_SESSION = [];
        session_destroy();
        redirect('/login');
    }
}
