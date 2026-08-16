<?php $this->layout('layouts/main', ['title' => 'Prenotazioni in attesa']) ?>

<h1>Prenotazioni in attesa: <?= $this->e($productName) ?></h1>
<p><a href="/admin/prenotazioni/in-attesa">← Torna ai prodotti</a></p>
<table><thead><tr><th>Cliente</th><th>Quantità</th><th>Totale stimato</th><th>Data</th></tr></thead>
<tbody><?php foreach ($reservations as $reservation): ?><tr>
    <td><?= $this->e($reservation['cliente_nome'] ?: $reservation['username']) ?></td><td><?= (int) $reservation['quantita'] ?></td><td>€ <?= number_format((float) $reservation['totale_stimato'], 2, ',', '.') ?></td><td><?= $this->e($reservation['created_at']) ?></td>
</tr><?php endforeach; ?></tbody></table>
