<?php

namespace Controller;

use Model\UserRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Util\Csrf;

final class AuthController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function loginForm(Request $request, Response $response): Response
    {
        if (!empty($_SESSION['user'])) {
            $destination = $_SESSION['user']['ruolo'] === 'cliente' ? '/prenotazioni' : '/admin/prodotti';
            return $response->withHeader('Location', $destination)->withStatus(302);
        }

        $engine = $this->container->get('template');
        $response->getBody()->write($engine->render('auth/login', ['errors' => []]));
        return $response;
    }

    public function login(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $username = trim((string) ($input['username'] ?? ''));
        $password = (string) ($input['password'] ?? '');
        $user = UserRepository::findByUsername($username);

        if (!Csrf::isValid($input['_csrf'] ?? null)) {
            $engine = $this->container->get('template');
            $response->getBody()->write($engine->render('auth/login', ['errors' => ['La sessione del modulo non è valida. Riprova.']]));
            return $response;
        }

        if (!$user || !password_verify($password, $user['password'])) {
            $engine = $this->container->get('template');
            $response->getBody()->write($engine->render('auth/login', ['errors' => ['Credenziali non valide.']]));
            return $response;
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
