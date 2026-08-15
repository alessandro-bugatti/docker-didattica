<!doctype html>
<html lang="it">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $this->e($title ?? 'Prodotti scout') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.classless.min.css">
    <style>
        .actions { display: flex; gap: .5rem; align-items: center; }
        .icon-button { display: inline-flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; padding: .5rem; margin: 0; }
        .icon-button svg { width: 1.15rem; height: 1.15rem; }
    </style>
</head>
<body>
<header class="container">
    <nav>
        <ul><li><strong>Autofinanziamento scout</strong></li></ul>
        <ul><li><a href="/prodotti">Prodotti</a></li><li><a href="/prodotti/nuovo" role="button">Nuovo prodotto</a></li></ul>
    </nav>
</header>
<main class="container">
    <?= $this->section('content') ?>
</main>
<footer class="container"><small>Progetto didattico SSR: PHP + Slim + Plates + PDO + MariaDB</small></footer>
</body>
</html>
