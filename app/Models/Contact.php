<?php
namespace App\Models;

use App\Core\Model;

class Contact extends Model
{
    protected string $table = 'contacts';

    /**
     * Get contacts with their tags
     */
    public function allWithTagsForUser(int $userId): array
    {
        $contacts = $this->allForUser($userId, 'name ASC, whatsapp ASC');

        if (empty($contacts))
            return [];

        $contactIds = array_column($contacts, 'id');
        $placeholders = implode(',', array_fill(0, count($contactIds), '?'));

        $sql = "SELECT ct.contact_id, t.id as tag_id, t.name as tag_name, t.color as tag_color
                FROM contact_tags ct
                JOIN tags t ON t.id = ct.tag_id
                WHERE ct.contact_id IN ({$placeholders})";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($contactIds);
        $tagRows = $stmt->fetchAll();

        // Group tags by contact_id
        $tagsByContact = [];
        foreach ($tagRows as $row) {
            $tagsByContact[$row['contact_id']][] = [
                'id' => $row['tag_id'],
                'name' => $row['tag_name'],
                'color' => $row['tag_color'],
            ];
        }

        // Attach tags to contacts
        foreach ($contacts as &$contact) {
            $contact['tags'] = $tagsByContact[$contact['id']] ?? [];
        }

        return $contacts;
    }

    /**
     * Get contacts by tag ID
     */
    public function getByTagForUser(int $tagId, int $userId): array
    {
        $sql = "SELECT c.* FROM contacts c
                JOIN contact_tags ct ON ct.contact_id = c.id
                WHERE c.user_id = :user_id AND ct.tag_id = :tag_id
                ORDER BY c.name ASC";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'tag_id' => $tagId]);
        return $stmt->fetchAll();
    }

    /**
     * Bulk insert contacts
     */
    public function bulkInsert(int $userId, array $contacts): int
    {
        $count = 0;
        $stmt = $this->db()->prepare(
            "INSERT INTO contacts (user_id, name, whatsapp) VALUES (:user_id, :name, :whatsapp)"
        );

        foreach ($contacts as $c) {
            $whatsapp = preg_replace('/[^0-9]/', '', $c['whatsapp']);
            if (empty($whatsapp))
                continue;

            $stmt->execute([
                'user_id' => $userId,
                'name' => $c['name'] ?? null,
                'whatsapp' => $whatsapp,
            ]);
            $count++;
        }

        return $count;
    }

    /**
     * Search contacts
     */
    public function searchForUser(int $userId, string $query): array
    {
        $sql = "SELECT * FROM contacts WHERE user_id = :user_id
                AND (name LIKE :query OR whatsapp LIKE :query)
                ORDER BY name ASC";
        $stmt = $this->db()->prepare($sql);
        $stmt->execute(['user_id' => $userId, 'query' => "%{$query}%"]);
        return $stmt->fetchAll();
    }
}
