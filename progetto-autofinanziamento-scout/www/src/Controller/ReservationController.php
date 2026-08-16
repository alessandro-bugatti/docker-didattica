<?php

namespace Controller;

use Model\ProductRepository;
use Model\ReservationRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Util\Csrf;
use Util\View;

final class ReservationController
{
    public function __construct(private ReservationRepository $reservations, private ProductRepository $products, private View $view) {}

    public function index(Request $request, Response $response): Response
    {
        $customerId = (int) $_SESSION['user']['id'];
        return $this->view->render($response, 'reservations/index', [
            'reservations' => $this->reservations->forCustomer($customerId),
            'total' => $this->reservations->totalForCustomer($customerId),
        ]);
    }

    public function adminIndex(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'reservations/admin', ['reservations' => $this->reservations->all()]);
    }

    public function store(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $productId = (int) ($input['prodotto_id'] ?? 0);
        $quantity = filter_var($input['quantita'] ?? null, FILTER_VALIDATE_INT);
        $product = $this->products->find($productId);
        $errors = [];
        if (!Csrf::isValid($input['_csrf'] ?? null)) $errors[] = 'La sessione del modulo non è valida. Riprova.';
        if (!$product) $errors[] = 'Il prodotto selezionato non esiste.';
        if ($quantity === false || $quantity < 1) $errors[] = 'Inserisci una quantità intera positiva.';
        if (!$errors && !$this->reservations->create((int) $_SESSION['user']['id'], $productId, $quantity)) $errors[] = 'La prenotazione non è stata confermata: la quantità richiesta non è più disponibile.';
        if ($errors) return $this->view->render($response, 'products/public', ['products' => $this->products->all(), 'errors' => $errors]);
        return $response->withHeader('Location', '/prenotazioni')->withStatus(302);
    }

    public function cancel(Request $request, Response $response, array $args): Response
    {
        $input = (array) $request->getParsedBody();
        if (!Csrf::isValid($input['_csrf'] ?? null)) return $response->withStatus(400);
        $this->reservations->cancel((int) $args['id'], (int) $_SESSION['user']['id']);
        return $response->withHeader('Location', '/prenotazioni')->withStatus(302);
    }
}
