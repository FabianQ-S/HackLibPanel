<?php

namespace App\Models;

class Book
{
    private \PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM books WHERE id = ?');
        $stmt->execute([$id]);
        $book = $stmt->fetch();
        return $book ?: null;
    }

    public function getAll(array $filters = [], int $limit = 50, int $offset = 0): array
    {
        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(titulo LIKE ? OR autor LIKE ? OR isbn LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['categoria'])) {
            $where[] = 'categoria = ?';
            $params[] = $filters['categoria'];
        }

        if (!empty($filters['nivel'])) {
            $where[] = 'nivel = ?';
            $params[] = $filters['nivel'];
        }

        if (!empty($filters['formato'])) {
            $where[] = 'formato = ?';
            $params[] = $filters['formato'];
        }

        $sql = 'SELECT * FROM books';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }
        $sql .= ' ORDER BY titulo ASC LIMIT ? OFFSET ?';
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function count(array $filters = []): int
    {
        $where = [];
        $params = [];

        if (!empty($filters['search'])) {
            $where[] = '(titulo LIKE ? OR autor LIKE ? OR isbn LIKE ?)';
            $search = '%' . $filters['search'] . '%';
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($filters['categoria'])) {
            $where[] = 'categoria = ?';
            $params[] = $filters['categoria'];
        }

        if (!empty($filters['nivel'])) {
            $where[] = 'nivel = ?';
            $params[] = $filters['nivel'];
        }

        $sql = 'SELECT COUNT(*) FROM books';
        if (!empty($where)) {
            $sql .= ' WHERE ' . implode(' AND ', $where);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO books (titulo, autor, categoria, nivel, version_tecnologia, formato, stock, precio, isbn, anio_publicacion) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $data['titulo'],
            $data['autor'],
            $data['categoria'],
            $data['nivel'],
            $data['version_tecnologia'] ?? '',
            $data['formato'],
            (int)($data['stock'] ?? 0),
            (float)($data['precio'] ?? 0),
            $data['isbn'] ?? null,
            (int)($data['anio_publicacion'] ?? date('Y')),
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE books SET titulo=?, autor=?, categoria=?, nivel=?, version_tecnologia=?, formato=?, stock=?, precio=?, isbn=?, anio_publicacion=?, updated_at=CURRENT_TIMESTAMP WHERE id=?'
        );
        return $stmt->execute([
            $data['titulo'],
            $data['autor'],
            $data['categoria'],
            $data['nivel'],
            $data['version_tecnologia'] ?? '',
            $data['formato'],
            (int)($data['stock'] ?? 0),
            (float)($data['precio'] ?? 0),
            $data['isbn'] ?? null,
            (int)($data['anio_publicacion'] ?? date('Y')),
            $id,
        ]);
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare('DELETE FROM books WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function decrementStock(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE books SET stock = stock - 1, updated_at = CURRENT_TIMESTAMP WHERE id = ? AND stock > 0');
        $stmt->execute([$id]);
        return $stmt->rowCount() > 0;
    }

    public function incrementStock(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE books SET stock = stock + 1, updated_at = CURRENT_TIMESTAMP WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function getStats(): array
    {
        $stats = [];
        
        // Total books
        $stmt = $this->db->query('SELECT COUNT(*) FROM books');
        $stats['total'] = (int)$stmt->fetchColumn();

        // Total stock
        $stmt = $this->db->query('SELECT COALESCE(SUM(stock), 0) FROM books');
        $stats['total_stock'] = (int)$stmt->fetchColumn();

        // By category
        $stmt = $this->db->query('SELECT categoria, COUNT(*) as count FROM books GROUP BY categoria');
        $stats['by_category'] = $stmt->fetchAll();

        // By level
        $stmt = $this->db->query('SELECT nivel, COUNT(*) as count FROM books GROUP BY nivel');
        $stats['by_level'] = $stmt->fetchAll();

        // Average price
        $stmt = $this->db->query('SELECT COALESCE(AVG(precio), 0) FROM books');
        $stats['avg_price'] = round((float)$stmt->fetchColumn(), 2);

        return $stats;
    }
}
