<?php
namespace App\Services;

/**
 * OpenAI / ChatGPT Integration Service
 * Personalizes messages using AI
 */
class OpenAIService
{
    private string $apiKey;
    private string $model;
    private string $apiUrl = 'https://api.openai.com/v1/chat/completions';

    public function __construct()
    {
        $this->apiKey = CONFIG['openai']['key'] ?? '';
        $this->model = CONFIG['openai']['model'] ?? 'gpt-4.1';
    }

    /**
     * Personalize a message template for a specific contact
     */
    public function personalize(string $template, array $contact, string $category = '', string $userPrompt = ''): string
    {
        if (empty($this->apiKey)) {
            // Fallback: return template with basic macro replacement
            return $template;
        }

        $contactName = $contact['name'] ?? 'Cliente';
        $contactPhone = $contact['whatsapp'] ?? '';

        $systemPrompt = "Você é um assistente especializado em personalizar mensagens de WhatsApp marketing.
Seu objetivo é pegar um template de mensagem e personalizá-lo para cada cliente individual,
mantendo o tom natural e humano, como se fosse escrito especificamente para aquela pessoa.

Regras:
- Mantenha a mensagem curta e objetiva (WhatsApp)
- Use linguagem natural e brasileira
- Não use emojis em excesso
- Preserve o sentido original da mensagem
- Adapte o tom baseado na categoria do cliente";

        if (!empty($userPrompt)) {
            $systemPrompt .= "\n\nInstruções adicionais do usuário:\n{$userPrompt}";
        }

        $userMessage = "Template da mensagem:\n\"{$template}\"\n\n";
        $userMessage .= "Dados do cliente:\n";
        $userMessage .= "- Nome: {$contactName}\n";
        $userMessage .= "- Telefone: {$contactPhone}\n";
        if (!empty($category)) {
            $userMessage .= "- Categoria: {$category}\n";
        }
        $userMessage .= "\nPersonalize esta mensagem para este cliente específico. Retorne APENAS a mensagem personalizada, sem explicações.";

        $payload = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage],
            ],
            'temperature' => 0.7,
            'max_tokens' => 500,
        ];

        $result = $this->request($payload);

        if (isset($result['choices'][0]['message']['content'])) {
            return trim($result['choices'][0]['message']['content']);
        }

        // Fallback to original template if AI fails
        return $template;
    }

    /**
     * Test API connection
     */
    public function testConnection(): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'error' => 'API key não configurada'];
        }

        $payload = [
            'model' => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => 'Diga "OK" apenas.'],
            ],
            'max_tokens' => 5,
        ];

        $result = $this->request($payload);

        if (isset($result['choices'])) {
            return ['success' => true, 'model' => $this->model];
        }

        return ['success' => false, 'error' => $result['error']['message'] ?? 'Erro desconhecido'];
    }

    /**
     * Make API request to OpenAI
     */
    private function request(array $payload): array
    {
        $ch = curl_init($this->apiUrl);

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                "Authorization: Bearer {$this->apiKey}",
            ],
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return ['error' => ['message' => "cURL Error: {$error}"]];
        }

        return json_decode($response, true) ?? ['error' => ['message' => 'Resposta inválida da API']];
    }
}
