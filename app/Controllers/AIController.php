<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Services\OpenAIService;

class AIController extends Controller
{
    /**
     * AJAX endpoint: personalize a message preview using AI
     */
    public function personalize(): void
    {
        $this->requireAuth();

        $template = $this->input('template', '');
        $name = $this->input('contact_name', 'Cliente');
        $phone = $this->input('contact_phone', '');
        $category = $this->input('category', '');
        $prompt = $this->input('prompt', '');

        if (empty($template)) {
            $this->json(['success' => false, 'error' => 'Template vazio.'], 400);
        }

        $contact = [
            'name' => $name,
            'whatsapp' => $phone,
        ];

        $aiService = new OpenAIService();
        $result = $aiService->personalize($template, $contact, $category, $prompt);

        $this->json([
            'success' => true,
            'personalized' => $result,
        ]);
    }
}
