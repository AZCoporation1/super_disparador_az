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
        $credentials = $userModel->getEvolutionCredentials($this->userId());

        $flash = $this->getFlash();

        $this->view('settings.index', [
            'user' => $user,
            'flash' => $flash,
            'credentials' => $credentials,
            'openaiConfigured' => !empty(CONFIG['openai']['key']),
        ]);
    }

    public function update(): void
    {
        $this->requireAuth();

        $action = $this->input('action', '');

        switch ($action) {
            case 'save_evolution_credentials':
                $this->saveEvolutionCredentials();
                return;

            case 'test_evolution':
                $this->testEvolutionConnection();
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

    /**
     * Save & test Evolution API credentials (AJAX)
     * Flow: test connection first → if open, save as active → else return error
     */
    private function saveEvolutionCredentials(): void
    {
        $baseUrl = trim($this->input('evolution_base_url', ''));
        $instanceName = trim($this->input('evolution_instance_name', ''));
        $token = trim($this->input('evolution_token', ''));

        // Validation
        if (empty($baseUrl) || empty($instanceName) || empty($token)) {
            $this->json([
                'success' => false,
                'error' => 'Preencha todos os campos: URL, Instância e Token.',
            ], 400);
            return;
        }

        // Validate URL format
        if (!filter_var($baseUrl, FILTER_VALIDATE_URL)) {
            $this->json([
                'success' => false,
                'error' => 'A URL informada não é válida. Use o formato: https://sua-api.com',
            ], 400);
            return;
        }

        try {
            // Test real connection using the provided credentials
            $evolution = new EvolutionAPI($baseUrl, $token);
            $result = $evolution->checkConnection($instanceName);

            if ($result['success']) {
                // Connection is open — save credentials as active
                $userModel = new User();
                $userModel->saveEvolutionCredentials(
                    $this->userId(),
                    $baseUrl,
                    $instanceName,
                    $token,
                    'active'
                );

                $this->json([
                    'success' => true,
                    'message' => 'Conexão verificada e credenciais salvas com sucesso! ✅',
                    'state' => $result['state'] ?? 'open',
                ]);
            } else {
                // Connection test failed — save credentials as inactive so user can fix later
                $userModel = new User();
                $userModel->saveEvolutionCredentials(
                    $this->userId(),
                    $baseUrl,
                    $instanceName,
                    $token,
                    'inactive'
                );

                $this->json([
                    'success' => false,
                    'error' => $result['error'] ?? 'Não foi possível validar a conexão.',
                    'http_code' => $result['http_code'] ?? 0,
                    'saved' => true,
                ]);
            }
        } catch (\Exception $e) {
            $this->json([
                'success' => false,
                'error' => 'Erro ao testar conexão: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test existing saved Evolution credentials (AJAX)
     */
    private function testEvolutionConnection(): void
    {
        $userModel = new User();
        $credentials = $userModel->getEvolutionCredentials($this->userId());

        if (!$credentials) {
            $this->json([
                'success' => false,
                'error' => 'Nenhuma credencial configurada. Salve suas credenciais primeiro.',
            ]);
            return;
        }

        try {
            $evolution = new EvolutionAPI($credentials['base_url'], $credentials['token']);
            $result = $evolution->checkConnection($credentials['instance_name']);

            // Update status based on result
            $newStatus = $result['success'] ? 'active' : 'inactive';
            $userModel->updateConnectionStatus($this->userId(), $newStatus);

            $this->json($result);
        } catch (\Exception $e) {
            $userModel->updateConnectionStatus($this->userId(), 'inactive');
            $this->json([
                'success' => false,
                'error' => 'Erro ao testar: ' . $e->getMessage(),
            ]);
        }
    }
}
