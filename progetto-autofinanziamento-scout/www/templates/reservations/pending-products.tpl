<?php $this->layout('layouts/main', ['title' => 'Prodotti in attesa']) ?>

<h1>Prodotti in attesa</h1>
<p><a href="/admin/prenotazioni">← Torna alle prenotazioni</a></p>
<table><thead><tr><th>Prodotto</th><th>Quantità in attesa</th><th>Prenotazioni</th><th></th></tr></thead>
<tbody><?php foreach ($products as $product): ?><tr>
    <td><?= $this->e($product['nome']) ?></td><td><?= (int) $product['quantita_in_attesa'] ?></td><td><?= (int) $product['prenotazioni_count'] ?></td><td><a href="/admin/prenotazioni/in-attesa/<?= (int) $product['id'] ?>" role="button">Dettaglio</a></td>
</tr><?php endforeach; ?></tbody></table>
