<?php $this->layout('layouts/main', ['title' => 'Errore']) ?>

<h1>Si è verificato un problema</h1>
<p><?= $this->e($message) ?></p>
<p><small>Codice errore: <?= (int) $statusCode ?></small></p>
<p><a href="/">Torna alla pagina iniziale</a></p>
