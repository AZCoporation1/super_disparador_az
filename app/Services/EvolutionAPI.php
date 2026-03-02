<?php
namespace App\Services;

/**
 * Evolution API Integration Service
 * Handles WhatsApp message sending via Evolution API
 */
class EvolutionAPI
{
    private string $baseUrl;
    private string $apiToken;

    public function __construct()
    {
        $this->baseUrl = rtrim(CONFIG['evolution']['url'] ?? '', '/');
        $this->apiToken = CONFIG['evolution']['token'] ?? '';
    }

    /**
     * Send a text message via WhatsApp
     */
    public function sendTextMessage(string $instance, string $phone, string $message): array
    {
        $phone = $this->normalizePhone($phone);

        $endpoint = "{$this->baseUrl}/message/sendText/{$instance}";

        $payload = [
            'number' => $phone,
            'text' => $message,
        ];

        return $this->request('POST', $endpoint, $payload);
    }

    /**
     * Check instance connection status
     */
    public function checkConnection(string $instance): array
    {
        $endpoint = "{$this->baseUrl}/instance/connectionState/{$instance}";
        return $this->request('GET', $endpoint);
    }

    /**
     * List all instances
     */
    public function listInstances(): array
    {
        $endpoint = "{$this->baseUrl}/instance/fetchInstances";
        return $this->request('GET', $endpoint);
    }

    /**
     * Create a new instance
     */
    public function createInstance(string $instanceName): array
    {
        $endpoint = "{$this->baseUrl}/instance/create";
        $payload = [
            'instanceName' => $instanceName,
            'integration' => 'WHATSAPP-BAILEYS',
        ];
        return $this->request('POST', $endpoint, $payload);
    }

    /**
     * Get QR code for connecting
     */
    public function getQRCode(string $instance): array
    {
        $endpoint = "{$this->baseUrl}/instance/connect/{$instance}";
        return $this->request('GET', $endpoint);
    }

    /**
     * Normalize phone number (Brazil format)
     */
    private function normalizePhone(string $phone): string
    {
        // Remove non-numeric chars
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // Add Brazil country code if not present
        if (strlen($phone) <= 11 && !str_starts_with($phone, '55')) {
            $phone = '55' . $phone;
        }

        return $phone;
    }

    /**
     * Make HTTP request to Evolution API
     */
    private function request(string $method, string $url, ?array $data = null): array
    {
        $ch = curl_init();

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];

        if (!empty($this->apiToken)) {
            $headers[] = "apikey: {$this->apiToken}";
        }

        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        if ($method === 'POST' && $data !== null) {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['success' => false, 'error' => "cURL Error: {$error}", 'http_code' => 0];
        }

        $decoded = json_decode($response, true) ?? [];

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'http_code' => $httpCode,
            'data' => $decoded,
        ];
    }
}
