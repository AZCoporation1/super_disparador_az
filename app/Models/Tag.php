<?php
namespace App\Models;

use App\Core\Model;

class Tag extends Model
{
    protected string $table = 'tags';

    /**
     * Get tags with contact count for user
     */
    public function allWithCountForUser(int $userId): array
    {
        $sql = "SELECT t.*, COUNT(ct.contact_id) as contact_count
                FROM tags t
                LEFT JOIN contact_tags ct ON ct.tag_id = t.id
                WHERE t.user_id = :user_id
                GROUP BY t.id
                ORDER BY t.name ASC";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll();
    }

    /**
     * Assign a tag to a contact
     */
    public function assignToContact(int $contactId, int $tagId): bool
    {
        $sql = "INSERT IGNORE INTO contact_tags (contact_id, tag_id) VALUES (:contact_id, :tag_id)";
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute(['contact_id' => $contactId, 'tag_id' => $tagId]);
    }

    /**
     * Remove a tag from a contact
     */
    public function removeFromContact(int $contactId, int $tagId): bool
    {
        $sql = "DELETE FROM contact_tags WHERE contact_id = :contact_id AND tag_id = :tag_id";
        $stmt = $this->db()->prepare($sql);
        return $stmt->execute(['contact_id' => $contactId, 'tag_id' => $tagId]);
    }

    /**
     * Bulk assign tag to multiple contacts
     */
    public function bulkAssign(array $contactIds, int $tagId): int
    {
        $count = 0;
        $stmt = $this->db()->prepare(
            "INSERT IGNORE INTO contact_tags (contact_id, tag_id) VALUES (:contact_id, :tag_id)"
        );
        foreach ($contactIds as $contactId) {
            $stmt->execute(['contact_id' => $contactId, 'tag_id' => $tagId]);
            $count++;
        }
        return $count;
    }
}
