<?php

namespace Middleware;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response;

final class CustomerMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): ResponseInterface
    {
        if (empty($_SESSION['user']) || $_SESSION['user']['ruolo'] !== 'cliente') {
            return (new Response())->withHeader('Location', '/login')->withStatus(302);
        }
        return $handler->handle($request);
    }
}
