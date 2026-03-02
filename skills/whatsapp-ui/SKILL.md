---
name: whatsapp-ui
description: Skill for building WhatsApp-like UI components including phone frame preview, chat bubbles, queue visualization with progress bars, and confetti celebration animation.
allowed-tools:
  - "Read"
  - "Write"
---

# WhatsApp-like UI & Feedback Skill

## Purpose
UI patterns for WhatsApp-style message preview, dispatch queue visualization, and celebration effects.

## Key Files
- `app/Views/messages/compose.php` — WhatsApp phone frame preview
- `app/Views/dispatch/queue.php` — Queue UI + confetti
- `app/Views/layouts/main.php` — Design system and base styles

## WhatsApp Phone Frame
- Dark header bar (WhatsApp dark green `#075e54`)
- Contact avatar with initials
- Chat background pattern (`#efeae2`)
- Green message bubble (`#dcf8c6`)
- Read receipts (blue double-check)
- Message input bar with send button

## Color Palette
```javascript
whatsapp: {
    light: '#dcf8c6',  // Message bubble
    dark: '#075e54',    // Header
    green: '#25d366',   // Send button
    bg: '#ece5dd',      // Chat background
}
```

## Confetti Animation
Canvas-based confetti with:
- 200 particles in 7 colors
- Random rotation speed
- Gravity simulation
- Fade out after 120 frames
- Auto-cleanup after 250 frames

## Progress Bar Component
```html
<div class="progress-bar">
    <div class="progress-bar-fill bg-gradient-to-r from-green-400 to-emerald-500"
         style="width: 60%"></div>
</div>
```

## Flash Messages
- Success: Green background with checkmark icon
- Error: Red background with X icon
- Animated entrance with `fadeIn` keyframe
