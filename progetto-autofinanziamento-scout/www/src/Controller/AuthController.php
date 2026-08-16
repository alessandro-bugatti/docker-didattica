<?php

namespace Controller;

use Model\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Util\Csrf;
use Util\View;

final class AuthController
{
    public function __construct(private UserRepository $users, private View $view) {}

    public function loginForm(Request $request, Response $response): Response
    {
        if (!empty($_SESSION['user'])) {
            $destination = $_SESSION['user']['ruolo'] === 'cliente' ? '/prenotazioni' : '/admin/prodotti';
            return $response->withHeader('Location', $destination)->withStatus(302);
        }

        return $this->view->render($response, 'auth/login', ['errors' => []]);
    }

    public function login(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $username = trim((string) ($input['username'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $user = $this->users->findByUsername($username);

        if (!Csrf::isValid($input['_csrf'] ?? null)) {
            return $this->view->render($response, 'auth/login', ['errors' => ['La sessione del modulo non è valida. Riprova.']]);
        }

        if (!$user || !password_verify($password, $user['password'])) {
            return $this->view->render($response, 'auth/login', ['errors' => ['Credenziali non valide.']]);
        }

        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => (int) $user['id'], 'username' => $user['username'], 'ruolo' => $user['ruolo'], 'nome' => $user['nome']];
        $destination = $user['ruolo'] === 'cliente' ? '/prenotazioni' : '/admin/prodotti';
        return $response->withHeader('Location', $destination)->withStatus(302);
    }

    public function logout(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        if (Csrf::isValid($input['_csrf'] ?? null)) {
            $_SESSION = [];
            session_destroy();
        }
        return $response->withHeader('Location', '/prodotti')->withStatus(302);
    }
}
