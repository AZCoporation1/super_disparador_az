<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected bool $userScoped = false;

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    public function createUser(string $name, string $email, string $password): int
    {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        return $this->create([
            'name' => $name,
            'email' => $email,
            'password_hash' => $hash,
        ]);
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function updateEvolutionInstance(int $userId, string $instance): bool
    {
        $stmt = $this->db()->prepare("UPDATE users SET evolution_instance = :instance WHERE id = :id");
        return $stmt->execute(['instance' => $instance, 'id' => $userId]);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db()->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch();
        return $result ?: null;
    }
}
