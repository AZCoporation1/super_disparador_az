<?php
namespace App\Core;

/**
 * Base Model with common CRUD operations (always user-scoped)
 */
class Model
{
    protected string $table = '';
    protected bool $userScoped = true;

    protected function db(): \PDO
    {
        return Database::getInstance();
    }

    /**
     * Get all records for current user
     */
    public function allForUser(int $userId, string $orderBy = 'created_at DESC'): array
    {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY {$orderBy}";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Find a single record by ID (user-scoped)
     */
    public function findByIdForUser(int $id, int $userId): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id AND user_id = :user_id LIMIT 1";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * Insert a record
     */
    public function create(array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($data);
        return (int) $this->db()->lastInsertId();
    }

    /**
     * Update a record by ID (user-scoped)
     */
    public function updateForUser(int $id, int $userId, array $data): bool
    {
        $setParts = [];
        foreach (array_keys($data) as $col) {
            $setParts[] = "{$col} = :{$col}";
        }
        $setStr = implode(', ', $setParts);
        $sql = "UPDATE {$this->table} SET {$setStr} WHERE id = :id AND user_id = :user_id";
        $data['id'] = $id;
        $data['user_id'] = $userId;
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute($data);
    }

    /**
     * Delete a record by ID (user-scoped)
     */
    public function deleteForUser(int $id, int $userId): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id AND user_id = :user_id";
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute(['id' => $id, 'user_id' => $userId]);
    }

    /**
     * Count records for user
     */
    public function countForUser(int $userId): int
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE user_id = :user_id";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return (int) $stmt->fetchColumn();
    }
}
