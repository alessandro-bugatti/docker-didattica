<?php $this->layout('layouts/main', ['title' => 'Clienti con prenotazioni']) ?>

<h1>Clienti con prenotazioni</h1>
<nav aria-label="Report prenotazioni">
    <a href="/admin/prenotazioni/stato-globale">Stato globale</a> ·
    <a href="/admin/prenotazioni/in-attesa">Prodotti in attesa</a> ·
    <a href="/admin/prenotazioni/consegnati">Report consegnati</a>
</nav>
<div style="overflow-x:auto"><table>
    <thead><tr><th>Cliente</th><th>Username</th><th>Prenotazioni attive</th><th></th></tr></thead>
    <tbody><?php foreach ($customers as $customer): ?><tr>
        <td><?= $this->e($customer['nome'] ?: $customer['username']) ?></td>
        <td><?= $this->e($customer['username']) ?></td>
        <td><?= (int) $customer['prenotazioni_count'] ?></td>
        <td><a href="/admin/prenotazioni/clienti/<?= (int) $customer['id'] ?>" role="button">Dettaglio</a></td>
    </tr><?php endforeach; ?></tbody>
</table></div>
