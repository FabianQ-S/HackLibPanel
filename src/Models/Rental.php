<?php

namespace App\Models;

class Rental
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, b.titulo as book_titulo, b.autor as book_autor, u.username as user_username 
             FROM rentals r 
             JOIN books b ON r.book_id = b.id 
             JOIN users u ON r.user_id = u.id 
             WHERE r.id = ?'
        );
        $stmt->execute([$id]);
        $rental = $stmt->fetch();
        return $rental ?: null;
    }

    public function findByCode(string $code): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, b.titulo as book_titulo, b.autor as book_autor, u.username as user_username 
             FROM rentals r 
             JOIN books b ON r.book_id = b.id 
             JOIN users u ON r.user_id = u.id 
             WHERE r.codigo = ?'
        );
        $stmt->execute([$code]);
        $rental = $stmt->fetch();
        return $rental ?: null;
    }

    public function create(int $userId, int $bookId): ?array
    {
        $bookModel = new Book();
        $book = $bookModel->findById($bookId);
        
        if (!$book || $book['stock'] <= 0) {
            return null;
        }

        $code = generate_rental_code();
        
        $this->db->beginTransaction();
        try {
            // Decrement stock
            if (!$bookModel->decrementStock($bookId)) {
                $this->db->rollBack();
                return null;
            }

            // Create rental
            $stmt = $this->db->prepare(
                'INSERT INTO rentals (user_id, book_id, codigo, estado) VALUES (?, ?, ?, ?)'
            );
            $stmt->execute([$userId, $bookId, $code, 'pendiente']);
            
            $this->db->commit();
            
            return $this->findById((int)$this->db->lastInsertId());
        } catch (\Exception $e) {
            $this->db->rollBack();
            return null;
        }
    }

    public function updateStatus(int $id, string $newStatus): bool
    {
        $rental = $this->findById($id);
        if (!$rental) return false;

        $holdingStates = ['pendiente', 'aprobado', 'prestado'];
        $restoredStates = ['devuelto', 'cancelado'];

        if (in_array($rental['estado'], $holdingStates) && in_array($newStatus, $restoredStates)) {
            $bookModel = new Book();
            $bookModel->incrementStock($rental['book_id']);
        } elseif (in_array($rental['estado'], $restoredStates) && in_array($newStatus, $holdingStates)) {
            $bookModel = new Book();
            $bookModel->decrementStock($rental['book_id']);
        }

        $stmt = $this->db->prepare(
            'UPDATE rentals SET estado = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?'
        );
        return $stmt->execute([$newStatus, $id]);
    }

    public function getAll(array $filters = []): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['estado'])) {
            $where[] = 'r.estado = ?';
            $params[] = $filters['estado'];
        }

        if (!empty($filters['user_id'])) {
            $where[] = 'r.user_id = ?';
            $params[] = $filters['user_id'];
        }

        if (!empty($filters['search'])) {
            $where[] = '(r.codigo LIKE ? OR b.titulo LIKE ? OR u.username LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $sql = 'SELECT r.*, b.titulo as book_titulo, b.autor as book_autor, u.username as user_username 
                FROM rentals r 
                JOIN books b ON r.book_id = b.id 
                JOIN users u ON r.user_id = u.id';
        
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY r.created_at DESC';

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function getByUser(int $userId): array
    {
        return $this->getAll(['user_id' => $userId]);
    }

    public function getPendingCount(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM rentals WHERE estado = 'pendiente'");
        return (int)$stmt->fetchColumn();
    }

    public function getActiveCount(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM rentals WHERE estado IN ('pendiente', 'aprobado', 'prestado')");
        return (int)$stmt->fetchColumn();
    }
}
