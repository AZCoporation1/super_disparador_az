---
name: tags-categories
description: Skill for managing tags/categories with user-scoped isolation, contact assignment, bulk operations, and filtering for dispatch targeting.
allowed-tools:
  - "Read"
  - "Write"
---

# Tags & Categories Skill

## Purpose
Manages tags/categories for organizing contacts with user-scoped isolation.

## Key Files
- `app/Models/Tag.php` — Tag model with pivot operations
- `app/Controllers/TagController.php` — CRUD endpoints
- `app/Views/tags/index.php` — Tag management UI

## Database Structure
```sql
-- Tags table (user-scoped)
CREATE TABLE tags (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    color VARCHAR(7) DEFAULT '#6366f1',
    UNIQUE KEY unique_tag_per_user (user_id, name)
);

-- Pivot table
CREATE TABLE contact_tags (
    contact_id INT NOT NULL,
    tag_id INT NOT NULL,
    UNIQUE KEY unique_contact_tag (contact_id, tag_id)
);
```

## Operations
- **Create/Update/Delete** tags (user-scoped)
- **Bulk assign** tag to multiple contacts
- **Filter contacts** by tag for dispatch targeting
- **Count contacts** per tag for overview
- Tags have **color** for visual distinction

## Usage for Dispatch
```php
$contactModel = new Contact();
$contacts = $contactModel->getByTagForUser($tagId, $userId);
// Returns all contacts that have this tag assigned
```
