<?php $this->layout('layouts/main', ['title' => 'Stato globale']) ?>

<h1>Stato globale attuale degli ordini</h1>
<p><a href="/admin/prenotazioni">← Torna alle prenotazioni</a></p>
<table><thead><tr><th>Prodotto</th><th>Quantità prenotata</th><th>Quantità totale</th><th>Quantità rimasta</th></tr></thead>
<tbody><?php foreach ($products as $product): ?><tr>
    <td><?= $this->e($product['nome']) ?></td><td><?= (int) $product['quantita_prenotata'] ?></td><td><?= (int) $product['quantita_totale'] ?></td><td><?= (int) $product['quantita_rimasta'] ?></td>
</tr><?php endforeach; ?></tbody></table>
