<?php

use Controller\ProductController;
use Controller\AuthController;
use Controller\ReservationController;
use DI\Container;
use League\Plates\Engine;
use Middleware\AdminMiddleware;
use Middleware\CustomerMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteCollectorProxy;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../conf/config.php';

session_set_cookie_params([
    'httponly' => true,
    'samesite' => 'Lax',
    'secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
]);
session_start();

$container = new Container();
$container->set('template', function (): Engine {
    $engine = new Engine(__DIR__ . '/../templates');
    $engine->setFileExtension('tpl');
    return $engine;
});

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(APP_ENV === 'development', true, true);

$app->get('/', fn (Request $request, Response $response)
        => $response->withHeader('Location', '/prodotti')
                    ->withStatus(302));
$app->get('/prodotti', ProductController::class . ':publicIndex');
$app->get('/login', AuthController::class . ':loginForm');
$app->post('/login', AuthController::class . ':login');
$app->post('/logout', AuthController::class . ':logout');

$app->group('', function (RouteCollectorProxy $customer): void {
    $customer->get('/prenotazioni', ReservationController::class . ':index');
    $customer->post('/prenotazioni', ReservationController::class . ':store');
    $customer->post('/prenotazioni/{id}/annulla', ReservationController::class . ':cancel');
})->add(new CustomerMiddleware());

$app->group('/admin', function (RouteCollectorProxy $admin): void {
    $admin->get('/prodotti', ProductController::class . ':adminIndex');
    $admin->get('/prodotti/nuovo', ProductController::class . ':create');
    $admin->post('/prodotti', ProductController::class . ':store');
    $admin->get('/prodotti/{id}/modifica', ProductController::class . ':edit');
    $admin->post('/prodotti/{id}', ProductController::class . ':update');
    $admin->post('/prodotti/{id}/elimina', ProductController::class . ':delete');
    $admin->get('/prenotazioni', ReservationController::class . ':adminIndex');
    $admin->get('/prenotazioni/stato-globale', ReservationController::class . ':globalStatus');
    $admin->get('/prenotazioni/in-attesa', ReservationController::class . ':pendingProducts');
    $admin->get('/prenotazioni/in-attesa/{id}', ReservationController::class . ':pendingProductDetail');
    $admin->get('/prenotazioni/consegnati', ReservationController::class . ':deliveredReport');
    $admin->get('/prenotazioni/clienti/{id}', ReservationController::class . ':adminCustomerDetail');
    $admin->post('/prenotazioni/{id}/stato/{cliente_id}', ReservationController::class . ':updateStatus');
})->add(new AdminMiddleware());

$app->run();
