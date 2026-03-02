---
name: evolution-api
description: Skill for integrating with Evolution API to send WhatsApp messages, validate connections, manage instances, and handle phone number normalization for Brazil.
allowed-tools:
  - "Read"
  - "Write"
---

# Evolution API Integration Skill

## Purpose
Provides patterns and reference for integrating with the Evolution API to send WhatsApp messages, manage instances, and validate connections.

## Key Files
- `app/Services/EvolutionAPI.php` — Main service class
- `app/Controllers/DispatchController.php` — Dispatch endpoint
- `app/Controllers/SettingsController.php` — Connection test endpoint

## API Endpoints Used

| Action | Method | Endpoint |
|--------|--------|----------|
| Send text message | POST | `/message/sendText/{instance}` |
| Check connection | GET | `/instance/connectionState/{instance}` |
| List instances | GET | `/instance/fetchInstances` |
| Create instance | POST | `/instance/create` |
| Get QR Code | GET | `/instance/connect/{instance}` |

## Environment Variables
```env
EVOLUTION_API_URL=https://your-evolution-api.com
EVOLUTION_API_TOKEN=your_api_token
```

## Phone Number Normalization (Brazil)
- Remove all non-numeric characters
- If ≤11 digits and doesn't start with "55", prepend "55" (Brazil country code)
- Format: `55` + DDD (2 digits) + number (8-9 digits)

## Usage Pattern
```php
$evolution = new \App\Services\EvolutionAPI();
$result = $evolution->sendTextMessage('my-instance', '5511999998888', 'Hello!');
if ($result['success']) {
    // Message sent
} else {
    // Handle error: $result['error']
}
```

## Error Handling
- Always check `$result['success']` before proceeding
- Log all dispatch attempts in `dispatch_logs` table
- cURL timeout set to 30 seconds
- SSL verification disabled for development (enable in production)
