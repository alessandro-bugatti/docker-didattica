<?php $this->layout('layouts/main', ['title' => 'Prenotazioni']) ?>

<h1>Prenotazioni</h1>
<div style="overflow-x:auto"><table>
    <thead><tr><th>Cliente</th><th>Prodotto</th><th>Quantità</th><th>Totale stimato</th><th>Stato</th><th>Creata</th><th>Annullata</th></tr></thead>
    <tbody><?php foreach ($reservations as $reservation): ?><tr class="<?= $reservation['stato'] === 'annullato' ? 'cancelled' : '' ?>">
        <td><?= $this->e($reservation['cliente_nome'] ?: $reservation['username']) ?></td>
        <td><?= $this->e($reservation['prodotto_nome']) ?></td>
        <td><?= (int) $reservation['quantita'] ?></td>
        <td>€ <?= number_format((float) $reservation['totale_stimato'], 2, ',', '.') ?></td>
        <td><?= $this->e(str_replace('_', ' ', $reservation['stato'])) ?></td>
        <td><?= $this->e($reservation['created_at']) ?></td>
        <td><?= $this->e($reservation['annullata_at'] ?? '') ?></td>
    </tr><?php endforeach; ?></tbody>
</table></div>
