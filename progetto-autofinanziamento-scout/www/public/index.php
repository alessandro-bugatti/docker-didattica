<?php

use Controller\ProductController;
use DI\Container;
use League\Plates\Engine;
use Model\ProductRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Util\View;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../conf/config.php';

$container = new Container();
$container->set(PDO::class, function (): PDO {
    return new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHAR,
        DB_USER,
        DB_PASSWORD,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
});
$container->set(Engine::class, function (): Engine {
    $engine = new Engine(__DIR__ . '/../templates');
    $engine->setFileExtension('tpl');
    return $engine;
});
$container->set(View::class, fn ($container) => new View($container->get(Engine::class)));
$container->set(ProductRepository::class, fn ($container) => new ProductRepository($container->get(PDO::class)));
$container->set(ProductController::class, fn ($container) => new ProductController(
    $container->get(ProductRepository::class), $container->get(View::class)
));

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware(APP_ENV === 'development', true, true);

$app->get('/', fn (Request $request, Response $response) => $response->withHeader('Location', '/prodotti')->withStatus(302));
$app->get('/prodotti', [ProductController::class, 'index']);
$app->get('/prodotti/nuovo', [ProductController::class, 'create']);
$app->post('/prodotti', [ProductController::class, 'store']);
$app->get('/prodotti/{id}/modifica', [ProductController::class, 'edit']);
$app->post('/prodotti/{id}', [ProductController::class, 'update']);
$app->post('/prodotti/{id}/elimina', [ProductController::class, 'delete']);

$app->run();
