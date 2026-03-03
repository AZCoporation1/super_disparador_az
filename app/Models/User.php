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

    // =========================================================
    // Evolution API — Per-user credentials (encrypted token)
    // =========================================================

    /**
     * Save Evolution API credentials for a user.
     * The token is encrypted with AES-256-CBC before storage.
     */
    public function saveEvolutionCredentials(int $userId, string $baseUrl, string $instanceName, string $token, string $status = 'active'): bool
    {
        $encryptedToken = $this->encryptToken($token);

        $stmt = $this->db()->prepare(
            "UPDATE users SET
                evolution_base_url = :base_url,
                evolution_instance_name = :instance_name,
                evolution_token = :token,
                evolution_connection_status = :status
             WHERE id = :id"
        );

        return $stmt->execute([
            'base_url' => rtrim($baseUrl, '/'),
            'instance_name' => trim($instanceName),
            'token' => $encryptedToken,
            'status' => $status,
            'id' => $userId,
        ]);
    }

    /**
     * Get decrypted Evolution API credentials for a user.
     * Returns null if no credentials are configured.
     */
    public function getEvolutionCredentials(int $userId): ?array
    {
        $stmt = $this->db()->prepare(
            "SELECT evolution_base_url, evolution_instance_name, evolution_token, evolution_connection_status
             FROM users WHERE id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $userId]);
        $row = $stmt->fetch();

        if (!$row || empty($row['evolution_base_url'])) {
            return null;
        }

        return [
            'base_url' => $row['evolution_base_url'],
            'instance_name' => $row['evolution_instance_name'],
            'token' => $this->decryptToken($row['evolution_token']),
            'connection_status' => $row['evolution_connection_status'],
        ];
    }

    /**
     * Update the connection status for a user.
     */
    public function updateConnectionStatus(int $userId, string $status): bool
    {
        $stmt = $this->db()->prepare(
            "UPDATE users SET evolution_connection_status = :status WHERE id = :id"
        );
        return $stmt->execute(['status' => $status, 'id' => $userId]);
    }

    // =========================================================
    // Encryption helpers (AES-256-CBC)
    // =========================================================

    private function getEncryptionKey(): string
    {
        $key = CONFIG['app']['key'] ?? '';
        // Derive a 32-byte key via SHA-256
        return hash('sha256', $key, true);
    }

    private function encryptToken(string $plainText): string
    {
        $key = $this->getEncryptionKey();
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($plainText, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        // Store IV + ciphertext as base64
        return base64_encode($iv . $encrypted);
    }

    private function decryptToken(string $cipherText): string
    {
        $key = $this->getEncryptionKey();
        $data = base64_decode($cipherText);

        if ($data === false || strlen($data) < 17) {
            return ''; // Invalid ciphertext
        }

        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        return $decrypted !== false ? $decrypted : '';
    }
}
