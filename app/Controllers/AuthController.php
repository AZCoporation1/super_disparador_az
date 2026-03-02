<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class AuthController extends Controller
{
    public function loginForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        $flash = $this->getFlash();
        $this->view('auth.login', ['flash' => $flash]);
    }

    public function login(): void
    {
        $email = trim($this->input('email', ''));
        $password = $this->input('password', '');

        if (empty($email) || empty($password)) {
            $this->setFlash('error', 'Preencha todos os campos.');
            $this->redirect('/login');
        }

        $userModel = new User();
        $user = $userModel->findByEmail($email);

        if (!$user || !$userModel->verifyPassword($password, $user['password_hash'])) {
            $this->setFlash('error', 'E-mail ou senha incorretos.');
            $this->redirect('/login');
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        $this->redirect('/dashboard');
    }

    public function registerForm(): void
    {
        if (isset($_SESSION['user_id'])) {
            $this->redirect('/dashboard');
        }
        $flash = $this->getFlash();
        $this->view('auth.register', ['flash' => $flash]);
    }

    public function register(): void
    {
        $name = trim($this->input('name', ''));
        $email = trim($this->input('email', ''));
        $password = $this->input('password', '');
        $confirm = $this->input('password_confirm', '');

        if (empty($name) || empty($email) || empty($password)) {
            $this->setFlash('error', 'Preencha todos os campos.');
            $this->redirect('/register');
        }

        if ($password !== $confirm) {
            $this->setFlash('error', 'As senhas não coincidem.');
            $this->redirect('/register');
        }

        if (strlen($password) < 6) {
            $this->setFlash('error', 'A senha deve ter pelo menos 6 caracteres.');
            $this->redirect('/register');
        }

        $userModel = new User();

        if ($userModel->findByEmail($email)) {
            $this->setFlash('error', 'Este e-mail já está cadastrado.');
            $this->redirect('/register');
        }

        $userId = $userModel->createUser($name, $email, $password);

        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_email'] = $email;

        $this->setFlash('success', 'Conta criada com sucesso! Bem-vindo(a)!');
        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/login');
    }
}
