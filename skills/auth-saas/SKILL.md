---
name: auth-saas
description: Skill for implementing session-based authentication with multi-tenant SaaS isolation. Each user can only see and manage their own data (contacts, tags, messages, logs).
allowed-tools:
  - "Read"
  - "Write"
---

# Authentication & Multi-User SaaS Skill

## Purpose
Session-based authentication with per-user data isolation for a SaaS architecture.

## Key Files
- `app/Controllers/AuthController.php` — Login, register, logout
- `app/Core/Controller.php` — `requireAuth()`, `userId()` helpers
- `app/Core/Model.php` — User-scoped CRUD operations
- `app/Views/auth/login.php` — Login page
- `app/Views/auth/register.php` — Registration page

## Data Isolation
Every table that stores user data has a `user_id` column:
- `contacts.user_id`
- `tags.user_id`
- `messages.user_id`
- `dispatch_logs.user_id`

All queries filter by `user_id`:
```php
// Base Model — all queries are user-scoped
public function allForUser(int $userId): array
{
    $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id";
    // ...
}
```

## Session Management
```php
// Login sets session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];

// Auth check (in base controller)
protected function requireAuth(): void {
    if (!isset($_SESSION['user_id'])) {
        $this->redirect('/login');
    }
}
```

## Password Security
- Hashing: `password_hash($password, PASSWORD_DEFAULT)` (bcrypt)
- Verification: `password_verify($input, $hash)`
- Minimum length: 6 characters

## Default Admin User
- Email: `admin@disparador.com`
- Password: `admin123`
- Created via `database/schema.sql`
