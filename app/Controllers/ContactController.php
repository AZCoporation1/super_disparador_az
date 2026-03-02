<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Contact;
use App\Models\Tag;
use App\Services\CSVImporter;

class ContactController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $contactModel = new Contact();
        $tagModel = new Tag();

        $search = $this->query('search', '');
        if (!empty($search)) {
            $contacts = $contactModel->searchForUser($this->userId(), $search);
        } else {
            $contacts = $contactModel->allWithTagsForUser($this->userId());
        }

        $tags = $tagModel->allWithCountForUser($this->userId());
        $flash = $this->getFlash();

        $this->view('contacts.index', [
            'contacts' => $contacts,
            'tags' => $tags,
            'search' => $search,
            'flash' => $flash,
        ]);
    }

    public function create(): void
    {
        $this->requireAuth();
        $flash = $this->getFlash();
        $this->view('contacts.create', ['flash' => $flash]);
    }

    public function store(): void
    {
        $this->requireAuth();

        $name = trim($this->input('name', ''));
        $whatsapp = preg_replace('/[^0-9]/', '', $this->input('whatsapp', ''));

        if (empty($whatsapp)) {
            $this->setFlash('error', 'O número de WhatsApp é obrigatório.');
            $this->redirect('/contacts/create');
        }

        $contactModel = new Contact();
        $contactModel->create([
            'user_id' => $this->userId(),
            'name' => $name ?: null,
            'whatsapp' => $whatsapp,
        ]);

        $this->setFlash('success', 'Contato adicionado com sucesso!');
        $this->redirect('/contacts');
    }

    public function edit(): void
    {
        $this->requireAuth();
        $id = (int) $this->query('id', 0);
        $contactModel = new Contact();
        $contact = $contactModel->findByIdForUser($id, $this->userId());

        if (!$contact) {
            $this->setFlash('error', 'Contato não encontrado.');
            $this->redirect('/contacts');
        }

        $flash = $this->getFlash();
        $this->view('contacts.edit', ['contact' => $contact, 'flash' => $flash]);
    }

    public function update(): void
    {
        $this->requireAuth();
        $id = (int) $this->input('id', 0);
        $name = trim($this->input('name', ''));
        $whatsapp = preg_replace('/[^0-9]/', '', $this->input('whatsapp', ''));

        if (empty($whatsapp)) {
            $this->setFlash('error', 'O número de WhatsApp é obrigatório.');
            $this->redirect("/contacts/edit?id={$id}");
        }

        $contactModel = new Contact();
        $contactModel->updateForUser($id, $this->userId(), [
            'name' => $name ?: null,
            'whatsapp' => $whatsapp,
        ]);

        $this->setFlash('success', 'Contato atualizado!');
        $this->redirect('/contacts');
    }

    public function delete(): void
    {
        $this->requireAuth();
        $id = (int) $this->input('id', 0);
        $contactModel = new Contact();
        $contactModel->deleteForUser($id, $this->userId());
        $this->setFlash('success', 'Contato excluído.');
        $this->redirect('/contacts');
    }

    public function importForm(): void
    {
        $this->requireAuth();
        $flash = $this->getFlash();
        $this->view('contacts.import', ['flash' => $flash]);
    }

    public function import(): void
    {
        $this->requireAuth();

        if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
            $this->setFlash('error', 'Erro no upload do arquivo. Tente novamente.');
            $this->redirect('/contacts/import');
        }

        $file = $_FILES['csv_file'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if ($ext !== 'csv') {
            $this->setFlash('error', 'Apenas arquivos .csv são aceitos.');
            $this->redirect('/contacts/import');
        }

        $importer = new CSVImporter();
        $contacts = $importer->parse($file['tmp_name']);

        $errors = $importer->getErrors();
        if (!empty($errors)) {
            $this->setFlash('error', implode(' | ', $errors));
            $this->redirect('/contacts/import');
        }

        if (empty($contacts)) {
            $this->setFlash('error', 'Nenhum contato válido encontrado no arquivo.');
            $this->redirect('/contacts/import');
        }

        $contactModel = new Contact();
        $count = $contactModel->bulkInsert($this->userId(), $contacts);

        $skipped = $importer->getSkippedCount();
        $msg = "{$count} contato(s) importado(s) com sucesso!";
        if ($skipped > 0) {
            $msg .= " ({$skipped} linha(s) sem WhatsApp ignorada(s))";
        }

        $this->setFlash('success', $msg);
        $this->redirect('/contacts');
    }

    public function downloadTemplate(): void
    {
        $file = BASE_PATH . '/storage/csv-template.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="modelo-contatos.csv"');
        // Add BOM for Excel compatibility
        echo "\xEF\xBB\xBF";
        readfile($file);
        exit;
    }

    public function assignTags(): void
    {
        $this->requireAuth();

        $contactIds = $this->input('contact_ids', []);
        $tagId = (int) $this->input('tag_id', 0);

        if (empty($contactIds) || $tagId === 0) {
            $this->json(['success' => false, 'message' => 'Selecione contatos e uma tag.'], 400);
        }

        $tagModel = new Tag();
        // Verify tag belongs to user
        $tag = $tagModel->findByIdForUser($tagId, $this->userId());
        if (!$tag) {
            $this->json(['success' => false, 'message' => 'Tag não encontrada.'], 404);
        }

        $count = $tagModel->bulkAssign($contactIds, $tagId);
        $this->json(['success' => true, 'message' => "{$count} contato(s) atribuído(s) à tag."]);
    }
}
