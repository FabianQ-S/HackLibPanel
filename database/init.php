<?php
/**
 * Script de inicialización de la base de datos
 * Crea las tablas, inserta el seed y el usuario maestro
 */

require_once __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config/app.php';
$dbPath = $config['db_path'];
$dbDir = dirname($dbPath);

if (!is_dir($dbDir)) {
    mkdir($dbDir, 0755, true);
}

// Delete existing database for fresh start
if (file_exists($dbPath)) {
    unlink($dbPath);
    echo "[*] Base de datos anterior eliminada.\n";
}

try {
    $pdo = new PDO('sqlite:' . $dbPath);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec('PRAGMA journal_mode=WAL');
    $pdo->exec('PRAGMA foreign_keys=ON');

    // Execute schema
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $pdo->exec($schema);
    echo "[+] Esquema creado correctamente.\n";

    // Execute seed
    $seed = file_get_contents(__DIR__ . '/seed.sql');
    $pdo->exec($seed);
    echo "[+] Datos de ejemplo insertados.\n";

    // Create master admin
    $masterAdmin = $config['master_admin'];
    $hashedPassword = password_hash($masterAdmin['password'], PASSWORD_BCRYPT);
    
    $stmt = $pdo->prepare('INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)');
    $stmt->execute([
        $masterAdmin['username'],
        $masterAdmin['email'],
        $hashedPassword,
        'master_admin'
    ]);
    echo "[+] Usuario maestro creado: {$masterAdmin['username']}\n";

    echo "\n[✓] Base de datos inicializada correctamente en: {$dbPath}\n";
    echo "[i] Credenciales del admin maestro:\n";
    echo "    Usuario: {$masterAdmin['username']}\n";
    echo "    Password: {$masterAdmin['password']}\n";

} catch (PDOException $e) {
    echo "[✗] Error: " . $e->getMessage() . "\n";
    exit(1);
}
