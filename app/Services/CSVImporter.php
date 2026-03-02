<?php
namespace App\Services;

/**
 * CSV Importer Service
 * Handles CSV import compatible with Google Sheets, Excel (Win/Mac)
 */
class CSVImporter
{
    private array $errors = [];
    private int $imported = 0;
    private int $skipped = 0;

    /**
     * Parse a CSV file and return contacts array
     */
    public function parse(string $filePath): array
    {
        $this->errors = [];
        $this->imported = 0;
        $this->skipped = 0;

        $content = file_get_contents($filePath);
        if ($content === false) {
            $this->errors[] = 'Não foi possível ler o arquivo.';
            return [];
        }

        // Remove BOM (Excel UTF-8 with BOM)
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^{$bom}/", '', $content);

        // Normalize line endings (Mac: \r, Win: \r\n, Unix: \n)
        $content = str_replace(["\r\n", "\r"], "\n", $content);

        $lines = explode("\n", $content);
        if (empty($lines)) {
            $this->errors[] = 'Arquivo vazio.';
            return [];
        }

        // Detect delimiter (comma or semicolon)
        $firstLine = $lines[0];
        $delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';

        // Parse header
        $headers = str_getcsv(array_shift($lines), $delimiter);
        $headers = array_map(function ($h) {
            return strtolower(trim($h));
        }, $headers);

        // Find column indexes
        $nameCol = $this->findColumn($headers, ['nome', 'name', 'nome completo', 'full name']);
        $phoneCol = $this->findColumn($headers, ['whatsapp', 'telefone', 'phone', 'celular', 'numero', 'número', 'tel']);

        if ($phoneCol === null) {
            $this->errors[] = 'Coluna de WhatsApp não encontrada. Use "whatsapp" ou "telefone" como nome da coluna.';
            return [];
        }

        $contacts = [];

        foreach ($lines as $lineNum => $line) {
            $line = trim($line);
            if (empty($line))
                continue;

            $row = str_getcsv($line, $delimiter);

            $phone = isset($row[$phoneCol]) ? trim($row[$phoneCol]) : '';
            $phone = preg_replace('/[^0-9]/', '', $phone);

            if (empty($phone)) {
                $this->skipped++;
                continue;
            }

            $name = null;
            if ($nameCol !== null && isset($row[$nameCol])) {
                $name = trim($row[$nameCol]);
                if (empty($name))
                    $name = null; // Name is optional
            }

            $contacts[] = [
                'name' => $name,
                'whatsapp' => $phone,
            ];
            $this->imported++;
        }

        return $contacts;
    }

    /**
     * Find a column index by possible names
     */
    private function findColumn(array $headers, array $possibleNames): ?int
    {
        foreach ($possibleNames as $name) {
            $index = array_search($name, $headers);
            if ($index !== false) {
                return $index;
            }
        }
        return null;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getImportedCount(): int
    {
        return $this->imported;
    }

    public function getSkippedCount(): int
    {
        return $this->skipped;
    }
}
