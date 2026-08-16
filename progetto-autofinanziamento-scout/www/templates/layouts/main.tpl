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
        .cancelled { text-decoration: line-through; opacity: .6; }
    </style>
</head>
<body>
<header class="container">
    <nav>
        <ul><li><strong>Autofinanziamento scout</strong></li></ul>
        <ul>
            <li><a href="/prodotti">Prodotti</a></li>
            <?php if (!empty($_SESSION['user'])): ?>
                <?php if ($_SESSION['user']['ruolo'] === 'cliente'): ?>
                    <li><a href="/prenotazioni">Le mie prenotazioni</a></li>
                <?php else: ?>
                    <li><a href="/admin/prodotti">Dashboard</a></li>
                    <li><a href="/admin/prenotazioni">Prenotazioni</a></li>
                <?php endif; ?>
                <li>
                    <form method="post" action="/logout" style="display:inline">
                        <input type="hidden" name="_csrf" value="<?= $this->e(\Util\Csrf::token()) ?>">
                        <button type="submit" class="secondary outline">Esci</button>
                    </form>
                </li>
            <?php else: ?>
                <li><a href="/login">Accedi</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main class="container">
    <?= $this->section('content') ?>
</main>
<footer class="container"><small>Progetto didattico SSR: PHP + Slim + Plates + PDO + MariaDB</small></footer>
</body>
</html>
