<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Tag;
use App\Models\DispatchLog;
use App\Models\User;
use App\Services\EvolutionAPI;
use App\Services\OpenAIService;

class DispatchController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $tagModel = new Tag();
        $messageModel = new Message();

        $tags = $tagModel->allWithCountForUser($this->userId());
        $messages = $messageModel->allForUser($this->userId());

        $flash = $this->getFlash();

        $this->view('dispatch.queue', [
            'tags' => $tags,
            'messages' => $messages,
            'flash' => $flash,
        ]);
    }

    /**
     * Prepare the dispatch queue (AJAX) — returns contact list for selected tags
     */
    public function prepare(): void
    {
        $this->requireAuth();

        $tagIds = $this->input('tag_ids', []);
        $messageId = (int) $this->input('message_id', 0);
        $contactIds = $this->input('contact_ids', []);

        $contactModel = new Contact();
        $contacts = [];

        if (!empty($contactIds)) {
            // Specific contacts selected
            foreach ($contactIds as $cid) {
                $c = $contactModel->findByIdForUser((int) $cid, $this->userId());
                if ($c)
                    $contacts[] = $c;
            }
        } elseif (!empty($tagIds)) {
            // Get contacts by tags
            foreach ($tagIds as $tagId) {
                $tagContacts = $contactModel->getByTagForUser((int) $tagId, $this->userId());
                foreach ($tagContacts as $c) {
                    $contacts[$c['id']] = $c; // dedup by id
                }
            }
            $contacts = array_values($contacts);
        } else {
            // All contacts
            $contacts = $contactModel->allForUser($this->userId());
        }

        // Get message template
        $template = '';
        $aiEnabled = false;
        $aiPrompt = '';
        if ($messageId > 0) {
            $messageModel = new Message();
            $msg = $messageModel->findByIdForUser($messageId, $this->userId());
            if ($msg) {
                $template = $msg['template_body'];
                $aiEnabled = (bool) $msg['ai_enabled'];
                $aiPrompt = $msg['ai_prompt'] ?? '';
            }
        }

        $this->json([
            'success' => true,
            'contacts' => $contacts,
            'template' => $template,
            'aiEnabled' => $aiEnabled,
            'aiPrompt' => $aiPrompt,
            'total' => count($contacts),
        ]);
    }

    /**
     * Send a single message (AJAX endpoint, called sequentially by JS)
     */
    public function send(): void
    {
        $this->requireAuth();

        $contactId = (int) $this->input('contact_id', 0);
        $messageId = (int) $this->input('message_id', 0);
        $template = $this->input('template', '');
        $aiEnabled = (bool) $this->input('ai_enabled', false);
        $aiPrompt = $this->input('ai_prompt', '');
        $instance = $this->input('instance', '');
        $dryRun = (bool) $this->input('dry_run', false);

        // Get contact
        $contactModel = new Contact();
        $contact = $contactModel->findByIdForUser($contactId, $this->userId());

        if (!$contact) {
            $this->json(['success' => false, 'error' => 'Contato não encontrado.'], 404);
        }

        // Apply macros
        $messageModel = new Message();
        $finalMessage = $messageModel->applyMacros($template, $contact);

        // Apply AI personalization if enabled
        if ($aiEnabled && !empty(CONFIG['openai']['key'])) {
            try {
                $aiService = new OpenAIService();
                $tagNames = '';
                if (!empty($contact['tags'])) {
                    $tagNames = implode(', ', array_column($contact['tags'], 'name'));
                }
                $finalMessage = $aiService->personalize($finalMessage, $contact, $tagNames, $aiPrompt);
            } catch (\Exception $e) {
                // If AI fails, use macro-substituted message
            }
        }

        $dispatchLog = new DispatchLog();

        // Dry run mode (for testing without Evolution API)
        if ($dryRun) {
            $logId = $dispatchLog->logDispatch(
                $this->userId(),
                $contactId,
                $messageId ?: null,
                $template,
                $finalMessage,
                'sent',
                null
            );

            $this->json([
                'success' => true,
                'message' => $finalMessage,
                'contact' => $contact['name'] ?? $contact['whatsapp'],
                'log_id' => $logId,
                'dry_run' => true,
            ]);
            return;
        }

        // Get Evolution API instance
        if (empty($instance)) {
            $userModel = new User();
            $user = $userModel->findById($this->userId());
            $instance = $user['evolution_instance'] ?? '';
        }

        if (empty($instance) || empty(CONFIG['evolution']['url'])) {
            $logId = $dispatchLog->logDispatch(
                $this->userId(),
                $contactId,
                $messageId ?: null,
                $template,
                $finalMessage,
                'failed',
                'Evolution API não configurada'
            );

            $this->json([
                'success' => false,
                'error' => 'Evolution API não configurada. Vá em Configurações para configurar.',
                'message' => $finalMessage,
                'log_id' => $logId,
            ]);
            return;
        }

        // Send via Evolution API
        $evolution = new EvolutionAPI();
        $result = $evolution->sendTextMessage($instance, $contact['whatsapp'], $finalMessage);

        if ($result['success']) {
            $logId = $dispatchLog->logDispatch(
                $this->userId(),
                $contactId,
                $messageId ?: null,
                $template,
                $finalMessage,
                'sent',
                null
            );

            $this->json([
                'success' => true,
                'message' => $finalMessage,
                'contact' => $contact['name'] ?? $contact['whatsapp'],
                'log_id' => $logId,
            ]);
        } else {
            $error = $result['error'] ?? ($result['data']['message'] ?? 'Erro desconhecido');
            $logId = $dispatchLog->logDispatch(
                $this->userId(),
                $contactId,
                $messageId ?: null,
                $template,
                $finalMessage,
                'failed',
                $error
            );

            $this->json([
                'success' => false,
                'error' => $error,
                'message' => $finalMessage,
                'log_id' => $logId,
            ]);
        }
    }

    /**
     * Show dispatch logs
     */
    public function logs(): void
    {
        $this->requireAuth();

        $dispatchLog = new DispatchLog();
        $logs = $dispatchLog->getRecentForUser($this->userId(), 200);
        $stats = $dispatchLog->getStatsForUser($this->userId());
        $flash = $this->getFlash();

        $this->view('dispatch.logs', [
            'logs' => $logs,
            'stats' => $stats ?: ['total' => 0, 'sent' => 0, 'failed' => 0, 'pending' => 0],
            'flash' => $flash,
        ]);
    }
}
