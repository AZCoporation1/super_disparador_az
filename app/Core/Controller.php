<?php
namespace App\Core;

/**
 * Base Controller
 */
class Controller
{
    /**
     * Render a view file with data
     */
    protected function view(string $viewPath, array $data = []): void
    {
        extract($data);
        $viewFile = BASE_PATH . '/app/Views/' . str_replace('.', '/', $viewPath) . '.php';

        if (!file_exists($viewFile)) {
            http_response_code(500);
            echo "View não encontrada: {$viewPath}";
            return;
        }

        require $viewFile;
    }

    /**
     * Redirect to a URL
     */
    protected function redirect(string $url): void
    {
        header("Location: {$url}");
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * Require authentication — redirect to login if not logged in
     */
    protected function requireAuth(): void
    {
        if (!isset($_SESSION['user_id'])) {
            $this->setFlash('error', 'Faça login para continuar.');
            $this->redirect('/login');
        }
    }

    /**
     * Get current user ID
     */
    protected function userId(): int
    {
        return (int) ($_SESSION['user_id'] ?? 0);
    }

    /**
     * Get current user name
     */
    protected function userName(): string
    {
        return $_SESSION['user_name'] ?? '';
    }

    /**
     * Set a flash message
     */
    protected function setFlash(string $type, string $message): void
    {
        $_SESSION['flash'] = ['type' => $type, 'message' => $message];
    }

    /**
     * Get and clear the flash message
     */
    protected function getFlash(): ?array
    {
        $flash = $_SESSION['flash'] ?? null;
        unset($_SESSION['flash']);
        return $flash;
    }

    /**
     * Get POST data
     */
    protected function input(string $key, $default = null)
    {
        return $_POST[$key] ?? $default;
    }

    /**
     * Get GET data
     */
    protected function query(string $key, $default = null)
    {
        return $_GET[$key] ?? $default;
    }
}
