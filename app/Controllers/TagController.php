<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Tag;

class TagController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $tagModel = new Tag();
        $tags = $tagModel->allWithCountForUser($this->userId());
        $flash = $this->getFlash();
        $this->view('tags.index', ['tags' => $tags, 'flash' => $flash]);
    }

    public function store(): void
    {
        $this->requireAuth();

        $name = trim($this->input('name', ''));
        $color = $this->input('color', '#6366f1');

        if (empty($name)) {
            $this->setFlash('error', 'O nome da tag é obrigatório.');
            $this->redirect('/tags');
        }

        $tagModel = new Tag();
        $tagModel->create([
            'user_id' => $this->userId(),
            'name' => $name,
            'color' => $color,
        ]);

        $this->setFlash('success', 'Tag criada com sucesso!');
        $this->redirect('/tags');
    }

    public function update(): void
    {
        $this->requireAuth();

        $id = (int) $this->input('id', 0);
        $name = trim($this->input('name', ''));
        $color = $this->input('color', '#6366f1');

        if (empty($name)) {
            $this->setFlash('error', 'O nome da tag é obrigatório.');
            $this->redirect('/tags');
        }

        $tagModel = new Tag();
        $tagModel->updateForUser($id, $this->userId(), [
            'name' => $name,
            'color' => $color,
        ]);

        $this->setFlash('success', 'Tag atualizada!');
        $this->redirect('/tags');
    }

    public function delete(): void
    {
        $this->requireAuth();
        $id = (int) $this->input('id', 0);
        $tagModel = new Tag();
        $tagModel->deleteForUser($id, $this->userId());
        $this->setFlash('success', 'Tag excluída.');
        $this->redirect('/tags');
    }
}
