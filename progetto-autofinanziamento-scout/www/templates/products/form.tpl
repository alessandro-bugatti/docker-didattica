<?php $this->layout('layouts/main', ['title' => $title]) ?>

<h1><?= $this->e($title) ?></h1>
<?php if ($errors): ?>
<article aria-label="Errori di validazione"><strong>Controlla i dati:</strong><ul><?php foreach ($errors as $error): ?><li><?= $this->e($error) ?></li><?php endforeach; ?></ul></article>
<?php endif; ?>
<form method="post" action="<?= $this->e($formAction) ?>" enctype="multipart/form-data">
    <input type="hidden" name="_csrf" value="<?= $this->e(\Util\Csrf::token()) ?>">
    <label>Nome
        <input name="nome" required maxlength="120" value="<?= $this->e($product['nome']) ?>">
    </label>
    <label>Descrizione breve
        <input name="descrizione" required maxlength="255" value="<?= $this->e($product['descrizione']) ?>">
    </label>
    <label>Immagine (opzionale, JPG/PNG/WebP)
        <input type="file" name="immagine" accept="image/jpeg,image/png,image/webp">
    </label>
    <?php if (!empty($product['immagine'])): ?><small>Immagine attuale: <?= $this->e($product['immagine']) ?>. Lascia vuoto per mantenerla.</small><?php endif; ?>
    <div class="grid">
        <label>Prezzo di vendita (€)
            <input type="number" name="prezzo" required min="0" step="0.01" value="<?= $this->e($product['prezzo']) ?>">
        </label>
        <label>Quantità in magazzino
            <input type="number" name="quantita" required min="0" step="1" value="<?= $this->e($product['quantita']) ?>">
        </label>
    </div>
    <button type="submit">Salva prodotto</button>
    <a href="/prodotti" role="button" class="secondary">Annulla</a>
</form>
