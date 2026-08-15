<?php $this->layout('layouts/main', ['title' => 'Prodotti']) ?>

<h1>Prodotti disponibili</h1>
<p>Scopri i prodotti disponibili per sostenere le attività del gruppo scout.</p>

<?php if (!$products): ?>
    <article>Non ci sono ancora prodotti disponibili.</article>
<?php else: ?>
<div style="overflow-x:auto">
<table>
    <thead><tr><th>Prodotto</th><th>Descrizione</th><th>Prezzo</th></tr></thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
            <td>
                <?php if ($product['immagine']): ?><img src="<?= $this->e(STORAGE_URL . $product['immagine']) ?>" alt="" width="64" height="64" style="object-fit:cover; border-radius:.25rem"><br><?php endif; ?>
                <strong><?= $this->e($product['nome']) ?></strong>
            </td>
            <td><?= $this->e($product['descrizione']) ?></td>
            <td>€ <?= number_format((float) $product['prezzo'], 2, ',', '.') ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
