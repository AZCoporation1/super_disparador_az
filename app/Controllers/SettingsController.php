<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;
use App\Services\EvolutionAPI;
use App\Services\OpenAIService;

class SettingsController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();

        $userModel = new User();
        $user = $userModel->findById($this->userId());

        $flash = $this->getFlash();

        $this->view('settings.index', [
            'user' => $user,
            'flash' => $flash,
            'evolutionConfigured' => !empty(CONFIG['evolution']['url']),
            'openaiConfigured' => !empty(CONFIG['openai']['key']),
        ]);
    }

    public function update(): void
    {
        $this->requireAuth();

        $action = $this->input('action', '');

        switch ($action) {
            case 'evolution_instance':
                $instance = trim($this->input('evolution_instance', ''));
                $userModel = new User();
                $userModel->updateEvolutionInstance($this->userId(), $instance);
                $this->setFlash('success', 'Instância Evolution API atualizada!');
                break;

            case 'test_evolution':
                $instance = trim($this->input('instance', ''));
                if (empty($instance) || empty(CONFIG['evolution']['url'])) {
                    $this->json(['success' => false, 'error' => 'Informe a URL da Evolution API no .env e o nome da instância.']);
                    return;
                }
                $evolution = new EvolutionAPI();
                $result = $evolution->checkConnection($instance);
                $this->json($result);
                return;

            case 'test_openai':
                $ai = new OpenAIService();
                $result = $ai->testConnection();
                $this->json($result);
                return;

            case 'update_profile':
                $name = trim($this->input('name', ''));
                $email = trim($this->input('email', ''));
                if (!empty($name) && !empty($email)) {
                    $userModel = new User();
                    $stmt = $userModel->findById($this->userId());
                    // Simple update
                    $db = \App\Core\Database::getInstance();
                    $upd = $db->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
                    $upd->execute(['name' => $name, 'email' => $email, 'id' => $this->userId()]);
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $this->setFlash('success', 'Perfil atualizado!');
                }
                break;

            default:
                $this->setFlash('error', 'Ação inválida.');
        }

        $this->redirect('/settings');
    }
}
