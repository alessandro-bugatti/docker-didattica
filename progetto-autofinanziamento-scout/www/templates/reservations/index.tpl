<?php $this->layout('layouts/main', ['title' => 'Le mie prenotazioni']) ?>

<h1>Le mie prenotazioni</h1>
<p>Le prenotazioni annullate restano visibili come storico.</p>
<?php if (!$reservations): ?>
    <article>Non hai ancora effettuato prenotazioni. <a href="/prodotti">Vai ai prodotti</a>.</article>
<?php else: ?>
<table>
    <thead><tr><th>Prodotto</th><th>Quantità</th><th>Totale stimato</th><th>Stato</th><th>Data</th><th></th></tr></thead>
    <tbody><?php foreach ($reservations as $reservation): ?><tr class="<?= $reservation['stato'] === 'annullato' ? 'cancelled' : '' ?>">
        <td><?= $this->e($reservation['prodotto_nome']) ?></td>
        <td><?= (int) $reservation['quantita'] ?></td>
        <td>€ <?= number_format((float) $reservation['totale_stimato'], 2, ',', '.') ?></td>
        <td><?= $this->e(str_replace('_', ' ', $reservation['stato'])) ?></td>
        <td><?= $this->e($reservation['created_at']) ?></td>
        <td><?php if ($reservation['stato'] === 'in_attesa'): ?><form method="post" action="/prenotazioni/<?= (int) $reservation['id'] ?>/annulla"><input type="hidden" name="_csrf" value="<?= $this->e(\Util\Csrf::token()) ?>"><button type="submit" class="secondary" onclick="return confirm('Annullare la prenotazione?')">Annulla</button></form><?php endif; ?></td>
    </tr><?php endforeach; ?></tbody>
    <tfoot><tr><th colspan="2">Totale da pagare</th><th>€ <?= number_format($total, 2, ',', '.') ?></th><th colspan="3"></th></tr></tfoot>
</table>
<?php endif; ?>
