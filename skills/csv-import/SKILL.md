---
name: csv-import
description: Skill for importing contacts from CSV files (Google Sheets, Excel Windows/Mac). Handles BOM removal, delimiter auto-detection, flexible column matching, and optional name fields.
allowed-tools:
  - "Read"
  - "Write"
---

# CSV Import Skill

## Purpose
Handles CSV contact import compatible with Google Sheets and Excel (Windows/Mac).

## Key Files
- `app/Services/CSVImporter.php` — Parse and validate CSV
- `app/Controllers/ContactController.php` — Upload endpoint
- `storage/csv-template.csv` — Downloadable template

## Compatibility Matrix

| Source | Delimiter | BOM | Line Ending | Supported |
|--------|-----------|-----|-------------|-----------|
| Google Sheets | `,` | No | `\n` | ✅ |
| Excel (Windows) | `;` | UTF-8 BOM | `\r\n` | ✅ |
| Excel (Mac) | `,` | No | `\r` | ✅ |
| Numbers (Mac) | `,` | No | `\n` | ✅ |

## Column Detection
Accepts these column name variations:

| Field | Accepted Names |
|-------|----------------|
| Name (optional) | `nome`, `name`, `nome completo`, `full name` |
| Phone (required) | `whatsapp`, `telefone`, `phone`, `celular`, `numero`, `número`, `tel` |

## Rules
1. **Name is optional** — empty name columns are accepted
2. **WhatsApp is required** — rows without phone numbers are skipped
3. **BOM is removed** — Excel UTF-8 BOM (`\xEF\xBB\xBF`) is stripped
4. **Delimiter auto-detection** — semicolons vs commas
5. **Phone normalization** — non-numeric characters removed

## CSV Template Format
```csv
nome,whatsapp
João Silva,5511999998888
Maria Santos,5521988887777
```

## Download Template with BOM
When serving the template, add BOM for Excel compatibility:
```php
echo "\xEF\xBB\xBF";
readfile($templatePath);
```
