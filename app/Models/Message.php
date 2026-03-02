<?php
namespace App\Models;

use App\Core\Model;

class Message extends Model
{
    protected string $table = 'messages';

    /**
     * Apply macros to a template for a specific contact
     */
    public function applyMacros(string $template, array $contact): string
    {
        $replacements = [
            '[nome]' => $contact['name'] ?? 'Cliente',
            '[whatsapp]' => $contact['whatsapp'] ?? '',
            '[telefone]' => $contact['whatsapp'] ?? '',
        ];

        // Also handle tags if present
        if (!empty($contact['tags'])) {
            $tagNames = array_column($contact['tags'], 'name');
            $replacements['[tag]'] = implode(', ', $tagNames);
            $replacements['[categoria]'] = implode(', ', $tagNames);
        } else {
            $replacements['[tag]'] = '';
            $replacements['[categoria]'] = '';
        }

        return str_replace(
            array_keys($replacements),
            array_values($replacements),
            $template
        );
    }
}
