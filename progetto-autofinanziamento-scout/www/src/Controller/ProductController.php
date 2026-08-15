<?php

namespace Controller;

use Model\ProductRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Psr7\UploadedFile;
use Util\Csrf;
use Util\View;

final class ProductController
{
    public function __construct(private ProductRepository $products, private View $view) {}

    public function publicIndex(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'products/public', ['products' => $this->products->all()]);
    }

    public function adminIndex(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'products/index', ['products' => $this->products->all()]);
    }

    public function create(Request $request, Response $response): Response
    {
        return $this->view->render($response, 'products/form', [
            'product' => $this->emptyProduct(), 'errors' => [], 'formAction' => '/admin/prodotti', 'title' => 'Nuovo prodotto'
        ]);
    }

    public function store(Request $request, Response $response): Response
    {
        if (!$this->validCsrf($request)) {
            return $this->view->render($response, 'products/form', ['product' => $this->emptyProduct(), 'errors' => ['La sessione del modulo non è valida. Riprova.'], 'formAction' => '/admin/prodotti', 'title' => 'Nuovo prodotto']);
        }
        $result = $this->validatedData($request);
        if ($result['errors']) {
            return $this->view->render($response, 'products/form', [
                'product' => $result['product'], 'errors' => $result['errors'], 'formAction' => '/admin/prodotti', 'title' => 'Nuovo prodotto'
            ]);
        }
        $result['data']['immagine'] = $this->saveImage($request->getUploadedFiles()['immagine'] ?? null);
        $this->products->create($result['data']);
        return $response->withHeader('Location', '/prodotti')->withStatus(302);
    }

    public function edit(Request $request, Response $response, array $args): Response
    {
        $product = $this->products->find((int) $args['id']);
        if (!$product) return $response->withStatus(404);
        return $this->view->render($response, 'products/form', [
            'product' => $product, 'errors' => [], 'formAction' => '/admin/prodotti/' . $product['id'], 'title' => 'Modifica prodotto'
        ]);
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $id = (int) $args['id'];
        $product = $this->products->find($id);
        if (!$product) return $response->withStatus(404);
        if (!$this->validCsrf($request)) {
            return $this->view->render($response, 'products/form', ['product' => $product, 'errors' => ['La sessione del modulo non è valida. Riprova.'], 'formAction' => '/admin/prodotti/' . $id, 'title' => 'Modifica prodotto']);
        }
        $result = $this->validatedData($request);
        if ($result['errors']) {
            $result['product']['id'] = $id;
            return $this->view->render($response, 'products/form', [
                'product' => $result['product'], 'errors' => $result['errors'], 'formAction' => '/admin/prodotti/' . $id, 'title' => 'Modifica prodotto'
            ]);
        }
        $result['data']['immagine'] = $this->saveImage($request->getUploadedFiles()['immagine'] ?? null) ?: $product['immagine'];
        $this->products->update($id, $result['data']);
        return $response->withHeader('Location', '/prodotti')->withStatus(302);
    }

    public function delete(Request $request, Response $response, array $args): Response
    {
        if (! $this->validCsrf($request)) return $response->withStatus(400);
        $this->products->delete((int) $args['id']);
        return $response->withHeader('Location', '/prodotti')->withStatus(302);
    }

    private function validCsrf(Request $request): bool
    {
        $input = (array) $request->getParsedBody();
        return Csrf::isValid($input['_csrf'] ?? null);
    }

    private function emptyProduct(): array
    { return ['nome' => '', 'descrizione' => '', 'immagine' => '', 'prezzo' => '', 'quantita' => 0]; }

    private function validatedData(Request $request): array
    {
        $input = (array) $request->getParsedBody();
        $product = [
            'nome' => trim((string) ($input['nome'] ?? '')),
            'descrizione' => trim((string) ($input['descrizione'] ?? '')),
            'prezzo' => str_replace(',', '.', trim((string) ($input['prezzo'] ?? ''))),
            'quantita' => trim((string) ($input['quantita'] ?? '')),
            'immagine' => '',
        ];
        $errors = [];
        if ($product['nome'] === '') $errors[] = 'Il nome è obbligatorio.';
        if ($product['descrizione'] === '') $errors[] = 'La descrizione breve è obbligatoria.';
        if (!is_numeric($product['prezzo']) || (float) $product['prezzo'] < 0) $errors[] = 'Inserisci un prezzo valido (maggiore o uguale a zero).';
        if (filter_var($product['quantita'], FILTER_VALIDATE_INT) === false || (int) $product['quantita'] < 0) $errors[] = 'Inserisci una quantità intera maggiore o uguale a zero.';
        return ['product' => $product, 'errors' => $errors, 'data' => [
            'nome' => $product['nome'], 'descrizione' => $product['descrizione'], 'immagine' => null,
            'prezzo' => number_format((float) $product['prezzo'], 2, '.', ''), 'quantita' => (int) $product['quantita']
        ]];
    }

    private function saveImage(?UploadedFile $file): ?string
    {
        if (!$file || $file->getError() !== UPLOAD_ERR_OK) return null;
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        if (!isset($allowed[$file->getClientMediaType()])) return null;
        if (!is_dir(STORAGE_PATH)) mkdir(STORAGE_PATH, 0775, true);
        $filename = bin2hex(random_bytes(12)) . '.' . $allowed[$file->getClientMediaType()];
        $file->moveTo(STORAGE_PATH . $filename);
        return $filename;
    }
}
