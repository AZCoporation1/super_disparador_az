---
name: templates-macros
description: Skill for creating message templates with macro placeholders ([nome], [whatsapp], [tag]) that get automatically substituted per contact before sending.
allowed-tools:
  - "Read"
  - "Write"
---

# Templates & Macros Skill

## Purpose
Message template system with automatic macro substitution for personalized WhatsApp messages.

## Key Files
- `app/Models/Message.php` — Template storage + macro engine
- `app/Controllers/MessageController.php` — Compose + preview
- `app/Views/messages/compose.php` — WhatsApp-like editor

## Available Macros

| Macro | Replaced With | Example |
|-------|---------------|---------|
| `[nome]` | Contact name (or "Cliente" if empty) | `Olá, João!` |
| `[whatsapp]` | Contact phone number | `5511999998888` |
| `[telefone]` | Same as `[whatsapp]` | `5511999998888` |
| `[tag]` | Contact's tag names (comma-separated) | `VIP, Premium` |
| `[categoria]` | Same as `[tag]` | `VIP, Premium` |

## Macro Substitution Engine
```php
$messageModel = new Message();
$finalText = $messageModel->applyMacros($template, $contact);
// "Olá, [nome]!" → "Olá, João Silva!"
```

## UI Features
- Click-to-insert macro buttons in composer
- Live preview while typing
- AJAX preview with real contact data substitution
- WhatsApp-style phone frame preview
