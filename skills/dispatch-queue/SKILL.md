---
name: dispatch-queue
description: Skill for sequential WhatsApp message dispatch with random delays (2-10 seconds) between messages, individual progress bars, pause/resume, and completion celebrations.
allowed-tools:
  - "Read"
  - "Write"
---

# Dispatch Queue & Random Delay Skill

## Purpose
Sequential message dispatch with random delays to prevent WhatsApp blocking, with visual progress tracking.

## Key Files
- `app/Controllers/DispatchController.php` — Backend send endpoint
- `app/Views/dispatch/queue.php` — Queue UI with progress bars

## Random Delay Logic
```javascript
// Random delay between 2-10 seconds
const delay = Math.floor(Math.random() * 8000) + 2000;
// = 2000ms to 10000ms
```

## Dispatch Flow
1. User selects message template + tags
2. `prepare()` returns contact list via AJAX
3. UI renders queue with pending items
4. User clicks "Play" → `sendNext()` starts
5. For each contact:
   a. Mark as "sending" (yellow badge, pulsing avatar)
   b. Start progress bar animation over `delay` ms
   c. After delay, POST to `/dispatch/send`
   d. Mark as "sent" (green) or "failed" (red)
   e. Move to next contact
6. On completion: confetti + congratulations modal

## Pause/Resume
- `isPaused` flag checked before each send
- Play/Pause buttons toggle visibility

## Dry Run Mode
- When enabled, messages are logged but NOT sent via Evolution API
- Useful for testing the queue flow without real sends

## Progress Tracking
- Individual progress bars per contact (CSS transition animation)
- Overall progress bar showing total completion percentage
- Status badges (Pending → Sending → Sent/Failed)
