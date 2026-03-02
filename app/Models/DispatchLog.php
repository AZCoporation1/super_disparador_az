<?php
namespace App\Models;

use App\Core\Model;

class DispatchLog extends Model
{
    protected string $table = 'dispatch_logs';

    /**
     * Create a log entry
     */
    public function logDispatch(int $userId, int $contactId, ?int $messageId, string $originalMessage, string $sentMessage, string $status, ?string $error = null): int
    {
        return $this->create([
            'user_id' => $userId,
            'contact_id' => $contactId,
            'message_id' => $messageId,
            'original_message' => $originalMessage,
            'sent_message' => $sentMessage,
            'status' => $status,
            'error_message' => $error,
            'sent_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Get recent logs with contact info
     */
    public function getRecentForUser(int $userId, int $limit = 100): array
    {
        $sql = "SELECT dl.*, c.name as contact_name, c.whatsapp as contact_whatsapp
                FROM dispatch_logs dl
                LEFT JOIN contacts c ON c.id = dl.contact_id
                WHERE dl.user_id = :user_id
                ORDER BY dl.created_at DESC
                LIMIT {$limit}";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Get stats for user
     */
    public function getStatsForUser(int $userId): array
    {
        $sql = "SELECT
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent,
                    SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
                FROM dispatch_logs WHERE user_id = :user_id";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetch();
    }
}
