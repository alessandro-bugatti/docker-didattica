<?php $this->layout('layouts/main', ['title' => 'Prodotti']) ?>

<h1>Prodotti disponibili</h1>
<p>Scopri i prodotti disponibili per sostenere le attività del gruppo scout.</p>

<?php if (!empty($errors)): ?><article aria-label="Errori di prenotazione"><ul><?php foreach ($errors as $error): ?><li><?= $this->e($error) ?></li><?php endforeach; ?></ul></article><?php endif; ?>

<?php if (!$products): ?>
    <article>Non ci sono ancora prodotti disponibili.</article>
<?php else: ?>

<table>
    <thead><tr><th>Prodotto</th><th>Descrizione</th><th>Prezzo</th><?php if (!empty($_SESSION['user']) && $_SESSION['user']['ruolo'] === 'cliente'): ?><th>Prenota</th><?php endif; ?></tr></thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
            <td>
                <?php if ($product['immagine']): ?><img src="<?= $this->e(STORAGE_URL . $product['immagine']) ?>" alt="" width="64" height="64" style="object-fit:cover; border-radius:.25rem"><br><?php endif; ?>
                <strong><?= $this->e($product['nome']) ?></strong>
            </td>
            <td><?= $this->e($product['descrizione']) ?></td>
            <td>€ <?= number_format((float) $product['prezzo'], 2, ',', '.') ?></td>
            <?php if (!empty($_SESSION['user']) && $_SESSION['user']['ruolo'] === 'cliente'): ?><td>
                <?php if ((int) $product['quantita'] > 0): ?>
                    <form method="post" action="/prenotazioni"><input type="hidden" name="_csrf" value="<?= $this->e(\Util\Csrf::token()) ?>"><input type="hidden" name="prodotto_id" value="<?= (int) $product['id'] ?>"><input type="number" name="quantita" min="1" max="<?= (int) $product['quantita'] ?>" value="1" required><button type="submit">Prenota</button></form>
                <?php else: ?><small>Esaurito</small><?php endif; ?>
            </td><?php endif; ?>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>
