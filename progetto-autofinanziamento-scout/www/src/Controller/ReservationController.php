<?php

namespace Controller;

use Model\ProductRepository;
use Model\ReservationRepository;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Util\Csrf;

final class ReservationController
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function index(Request $request, Response $response): Response
    {
        $customerId = (int) $_SESSION['user']['id'];
        return $this->render($response, 'reservations/index', [
            'reservations' => ReservationRepository::forCustomer($customerId),
            'total' => ReservationRepository::totalForCustomer($customerId),
        ]);
    }

    public function adminIndex(Request $request, Response $response): Response
    {
        return $this->render($response, 'reservations/admin', [
            'customers' => ReservationRepository::customersWithReservations(),
        ]);
    }

    public function adminCustomerDetail(Request $request, Response $response, array $args): Response
    {
        $reservations = ReservationRepository::forCustomerAsAdmin((int) $args['id']);
        if (!$reservations) return $response->withStatus(404);
        return $this->render($response, 'reservations/admin-detail', [
            'reservations' => $reservations,
            'total' => ReservationRepository::totalForCustomer((int) $args['id']),
            'customerName' => $reservations[0]['cliente_nome'] ?: $reservations[0]['username'],
        ]);
    }

    public function updateStatus(Request $request, Response $response, array $args): Response
    {
        $input = (array) $request->getParsedBody();
        if (!Csrf::isValid($input['_csrf'] ?? null)) return $response->withStatus(400);
        $status = (string) ($input['stato'] ?? '');
        ReservationRepository::setDelivered((int) $args['id'], $status);
        return $response->withHeader('Location', '/admin/prenotazioni/clienti/' . (int) $args['cliente_id'])->withStatus(302);
    }

    public function globalStatus(Request $request, Response $response): Response
    {
        return $this->render($response, 'reservations/global-status', [
            'products' => ReservationRepository::globalStatus(),
        ]);
    }

    public function pendingProducts(Request $request, Response $response): Response
    {
        return $this->render($response, 'reservations/pending-products', [
            'products' => ReservationRepository::productsWithPendingReservations(),
        ]);
    }

    public function pendingProductDetail(Request $request, Response $response, array $args): Response
    {
        $reservations = ReservationRepository::pendingForProduct((int) $args['id']);
        if (!$reservations) return $response->withStatus(404);
        return $this->render($response, 'reservations/pending-detail', [
            'reservations' => $reservations,
            'productName' => $reservations[0]['prodotto_nome'],
        ]);
    }

    public function deliveredReport(Request $request, Response $response): Response
    {
        return $this->render($response, 'reservations/delivered-report', [
            'products' => ReservationRepository::deliveredReport(),
            'totals' => ReservationRepository::deliveredTotals(),
        ]);
    }

    public function store(Request $request, Response $response): Response
    {
        $input = (array) $request->getParsedBody();
        $productId = (int) ($input['prodotto_id'] ?? 0);
        $quantity = filter_var($input['quantita'] ?? null, FILTER_VALIDATE_INT);
        $product = ProductRepository::find($productId);
        $errors = [];
        if (!Csrf::isValid($input['_csrf'] ?? null)) $errors[] = 'La sessione del modulo non è valida. Riprova.';
        if (!$product) $errors[] = 'Il prodotto selezionato non esiste.';
        if ($quantity === false || $quantity < 1) $errors[] = 'Inserisci una quantità intera positiva.';
        if (!$errors && !ReservationRepository::create((int) $_SESSION['user']['id'], $productId, $quantity)) $errors[] = 'La prenotazione non è stata confermata: la quantità richiesta non è più disponibile.';
        if ($errors) return $this->render($response, 'products/public', ['products' => ProductRepository::all(), 'errors' => $errors]);
        return $response->withHeader('Location', '/prenotazioni')->withStatus(302);
    }

    public function cancel(Request $request, Response $response, array $args): Response
    {
        $input = (array) $request->getParsedBody();
        if (!Csrf::isValid($input['_csrf'] ?? null)) return $response->withStatus(400);
        ReservationRepository::cancel((int) $args['id'], (int) $_SESSION['user']['id']);
        return $response->withHeader('Location', '/prenotazioni')->withStatus(302);
    }

    private function render(Response $response, string $template, array $data = []): Response
    {
        $engine = $this->container->get('template');
        $response->getBody()->write($engine->render($template, $data));
        return $response;
    }
}
