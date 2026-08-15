<?php $this->layout('layouts/main', ['title' => 'Prodotti']) ?>

<h1>Dashboard prodotti</h1>
<p>Gestisci il catalogo dei prodotti disponibili per l'autofinanziamento.</p>

<?php if (!$products): ?>
    <article>Non ci sono ancora prodotti. <a href="/admin/prodotti/nuovo">Inserisci il primo prodotto</a>.</article>
<?php else: ?>
<div style="overflow-x:auto">
<table>
    <thead><tr><th>Prodotto</th><th>Descrizione</th><th>Prezzo</th><th>Magazzino</th><th>Azioni</th></tr></thead>
    <tbody>
    <?php foreach ($products as $product): ?>
        <tr>
            <td>
                <?php if ($product['immagine']): ?><img src="<?= $this->e(STORAGE_URL . $product['immagine']) ?>" alt="" width="64" height="64" style="object-fit:cover; border-radius:.25rem"><br><?php endif; ?>
                <strong><?= $this->e($product['nome']) ?></strong>
            </td>
            <td><?= $this->e($product['descrizione']) ?></td>
            <td>€ <?= number_format((float) $product['prezzo'], 2, ',', '.') ?></td>
            <td><?= (int) $product['quantita'] ?></td>
            <td>
                <div class="actions">
                    <a class="icon-button secondary outline" href="/admin/prodotti/<?= (int) $product['id'] ?>/modifica" aria-label="Modifica <?= $this->e($product['nome']) ?>" title="Modifica">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L8 18l-4 1 1-4Z"/></svg>
                    </a>
                    <form method="post" action="/admin/prodotti/<?= (int) $product['id'] ?>/elimina" style="display:inline" onsubmit="return confirm('Eliminare questo prodotto?')">
                        <input type="hidden" name="_csrf" value="<?= $this->e(\Util\Csrf::token()) ?>">
                        <button type="submit" class="icon-button secondary outline" aria-label="Elimina <?= $this->e($product['nome']) ?>" title="Elimina">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 6h18"/><path d="M8 6V4h8v2"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v5M14 11v5"/></svg>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</div>
<?php endif; ?>
