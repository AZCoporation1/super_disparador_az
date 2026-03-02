<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Message;
use App\Models\Tag;
use App\Models\Contact;

class MessageController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $messageModel = new Message();
        $messages = $messageModel->allForUser($this->userId());
        $flash = $this->getFlash();
        $this->view('messages.index', ['messages' => $messages, 'flash' => $flash]);
    }

    public function compose(): void
    {
        $this->requireAuth();

        $tagModel = new Tag();
        $contactModel = new Contact();

        $tags = $tagModel->allWithCountForUser($this->userId());
        $contacts = $contactModel->allWithTagsForUser($this->userId());

        // Load existing message if editing
        $messageId = (int) $this->query('id', 0);
        $message = null;
        if ($messageId > 0) {
            $messageModel = new Message();
            $message = $messageModel->findByIdForUser($messageId, $this->userId());
        }

        $flash = $this->getFlash();

        $this->view('messages.compose', [
            'tags' => $tags,
            'contacts' => $contacts,
            'message' => $message,
            'flash' => $flash,
        ]);
    }

    public function store(): void
    {
        $this->requireAuth();

        $title = trim($this->input('title', 'Sem título'));
        $body = $this->input('template_body', '');
        $aiEnabled = $this->input('ai_enabled', '0');
        $aiPrompt = trim($this->input('ai_prompt', ''));

        if (empty($body)) {
            $this->setFlash('error', 'O corpo da mensagem é obrigatório.');
            $this->redirect('/messages/compose');
        }

        $messageModel = new Message();

        $id = (int) $this->input('id', 0);
        if ($id > 0) {
            // Update existing
            $messageModel->updateForUser($id, $this->userId(), [
                'title' => $title,
                'template_body' => $body,
                'ai_enabled' => $aiEnabled ? 1 : 0,
                'ai_prompt' => $aiPrompt,
            ]);
            $this->setFlash('success', 'Mensagem atualizada!');
        } else {
            // Create new
            $messageModel->create([
                'user_id' => $this->userId(),
                'title' => $title,
                'template_body' => $body,
                'ai_enabled' => $aiEnabled ? 1 : 0,
                'ai_prompt' => $aiPrompt,
            ]);
            $this->setFlash('success', 'Mensagem salva com sucesso!');
        }

        $this->redirect('/messages');
    }

    public function preview(): void
    {
        $this->requireAuth();

        $template = $this->input('template', '');
        $contactId = (int) $this->input('contact_id', 0);

        $contactModel = new Contact();
        $messageModel = new Message();

        // Get a sample contact for preview
        if ($contactId > 0) {
            $contact = $contactModel->findByIdForUser($contactId, $this->userId());
        } else {
            // Use first contact
            $contacts = $contactModel->allForUser($this->userId(), 'id ASC');
            $contact = $contacts[0] ?? ['name' => 'João', 'whatsapp' => '5511999999999'];
        }

        $preview = $messageModel->applyMacros($template, $contact);

        $this->json([
            'success' => true,
            'preview' => $preview,
            'contact' => [
                'name' => $contact['name'] ?? 'Cliente',
                'whatsapp' => $contact['whatsapp'] ?? '',
            ],
        ]);
    }

    public function delete(): void
    {
        $this->requireAuth();
        $id = (int) $this->input('id', 0);
        $messageModel = new Message();
        $messageModel->deleteForUser($id, $this->userId());
        $this->setFlash('success', 'Mensagem excluída.');
        $this->redirect('/messages');
    }
}
