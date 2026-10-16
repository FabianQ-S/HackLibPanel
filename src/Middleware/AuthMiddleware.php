<?php

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Require authentication - redirects to login if not authenticated
     */
    public static function requireAuth(): void
    {
        if (!is_authenticated()) {
            flash('error', 'Debes iniciar sesión para acceder a esta página.');
            redirect('/login');
        }
    }

    /**
     * Require admin role (master_admin or admin)
     */
    public static function requireAdmin(): void
    {
        self::requireAuth();
        if (!is_admin()) {
            flash('error', 'No tienes permisos para acceder a esta sección.');
            redirect('/');
        }
    }

    /**
     * Require master admin role
     */
    public static function requireMasterAdmin(): void
    {
        self::requireAuth();
        if (!is_master_admin()) {
            flash('error', 'Solo el administrador maestro puede realizar esta acción.');
            redirect('/admin');
        }
    }

    /**
     * Redirect if already authenticated
     */
    public static function redirectIfAuth(): void
    {
        if (is_authenticated()) {
            if (is_admin()) {
                redirect('/admin');
            }
            redirect('/catalog');
        }
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrf(): void
    {
        $token = $_POST['_csrf_token'] ?? '';
        if (!verify_csrf($token)) {
            http_response_code(403);
            flash('error', 'Token de seguridad inválido. Intenta de nuevo.');
            redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }
}
