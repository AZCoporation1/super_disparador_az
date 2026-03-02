---
name: openai-integration
description: Skill for integrating OpenAI ChatGPT API to personalize WhatsApp marketing messages per contact, category, and custom prompts. Supports configurable model via environment variable.
allowed-tools:
  - "Read"
  - "Write"
---

# OpenAI / ChatGPT Integration Skill

## Purpose
AI-powered message personalization using OpenAI API for WhatsApp marketing messages.

## Key Files
- `app/Services/OpenAIService.php` — API integration
- `app/Controllers/AIController.php` — AJAX preview endpoint
- `app/Controllers/DispatchController.php` — Uses AI during send

## Environment Variables
```env
OPENAI_API_KEY=sk-your-key
OPENAI_MODEL=gpt-4.1
```

## Personalization Flow
1. Template with macros is created by user
2. Macros are first substituted (name, phone, etc.)
3. If AI is enabled, the substituted message + contact context is sent to ChatGPT
4. ChatGPT returns a personalized version
5. Personalized message is sent via Evolution API

## System Prompt
The AI receives:
- System role: "WhatsApp marketing message personalizer"
- Contact data: name, phone, category
- User's custom prompt (if provided)
- Instructions: keep it short, natural, Brazilian Portuguese

## Fallback
If AI fails or API key is not set, the original macro-substituted message is used.

## Usage
```php
$ai = new \App\Services\OpenAIService();
$personalized = $ai->personalize(
    $template,     // "Olá, João! Temos novidades!"
    $contact,      // ['name' => 'João', 'whatsapp' => '5511...']
    'VIP',         // category
    'Seja amigável' // custom prompt
);
```

## Connection Test
```php
$ai = new \App\Services\OpenAIService();
$result = $ai->testConnection();
// Returns: ['success' => true, 'model' => 'gpt-4.1']
```
