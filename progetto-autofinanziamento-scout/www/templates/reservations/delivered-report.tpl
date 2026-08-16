<?php $this->layout('layouts/main', ['title' => 'Report consegnati']) ?>

<h1>Prodotti consegnati</h1>
<p><a href="/admin/prenotazioni">← Torna alle prenotazioni</a></p>
<table><thead><tr><th>Prodotto</th><th>Quantità consegnata</th><th>Ricavo totale</th></tr></thead>
<tbody><?php foreach ($products as $product): ?><tr>
    <td><?= $this->e($product['nome']) ?></td><td><?= (int) $product['quantita_consegnata'] ?></td><td>€ <?= number_format((float) $product['ricavo_totale'], 2, ',', '.') ?></td>
</tr><?php endforeach; ?></tbody>
<tfoot><tr><th>Totale</th><th><?= (int) $totals['quantita'] ?></th><th>€ <?= number_format((float) $totals['ricavo'], 2, ',', '.') ?></th></tr></tfoot></table>
