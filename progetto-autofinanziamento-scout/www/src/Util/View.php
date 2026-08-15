<?php

namespace Util;

use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;

final class View
{
    public function __construct(private Engine $templates) {}

    public function render(ResponseInterface $response, string $template, array $data = []): ResponseInterface
    {
        $response->getBody()->write($this->templates->render($template, $data));
        return $response;
    }
}
