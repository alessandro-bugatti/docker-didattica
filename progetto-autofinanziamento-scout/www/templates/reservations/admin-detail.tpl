<?php $this->layout('layouts/main', ['title' => 'Prenotazioni cliente']) ?>

<h1>Prenotazioni di <?= $this->e($customerName) ?></h1>
<p><a href="/admin/prenotazioni">← Torna ai clienti</a></p>
<div style="overflow-x:auto"><table>
    <thead><tr><th>Prodotto</th><th>Quantità</th><th>Totale stimato</th><th>Stato</th><th>Data</th><th></th></tr></thead>
    <tbody><?php foreach ($reservations as $reservation): ?><tr class="<?= $reservation['stato'] === 'annullato' ? 'cancelled' : '' ?>">
        <td><?= $this->e($reservation['prodotto_nome']) ?></td>
        <td><?= (int) $reservation['quantita'] ?></td>
        <td>€ <?= number_format((float) $reservation['totale_stimato'], 2, ',', '.') ?></td>
        <td><?= $this->e(str_replace('_', ' ', $reservation['stato'])) ?></td>
        <td><?= $this->e($reservation['created_at']) ?></td>
        <td><?php if ($reservation['stato'] !== 'annullato'): ?><form method="post" action="/admin/prenotazioni/<?= (int) $reservation['id'] ?>/stato/<?= (int) $reservation['cliente_id'] ?>"><input type="hidden" name="_csrf" value="<?= $this->e(\Util\Csrf::token()) ?>"><input type="hidden" name="stato" value="<?= $reservation['stato'] === 'consegnato' ? 'in_attesa' : 'consegnato' ?>"><button type="submit"><?= $reservation['stato'] === 'consegnato' ? 'Riporta in attesa' : 'Segna consegnato' ?></button></form><?php endif; ?></td>
    </tr><?php endforeach; ?></tbody>
    <tfoot><tr><th colspan="2">Totale da pagare</th><th>€ <?= number_format($total, 2, ',', '.') ?></th><th colspan="3"></th></tr></tfoot>
</table></div>
