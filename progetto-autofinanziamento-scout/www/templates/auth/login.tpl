<?php $this->layout('layouts/main', ['title' => 'Accesso']) ?>

<h1>Accesso</h1>
<p>Accedi per gestire i prodotti oppure le tue prenotazioni.</p>

<?php if ($errors): ?>
<article aria-label="Errori di accesso"><ul><?php foreach ($errors as $error): ?><li><?= $this->e($error) ?></li><?php endforeach; ?></ul></article>
<?php endif; ?>

<form method="post" action="/login">
    <input type="hidden" name="_csrf" value="<?= $this->e(\Util\Csrf::token()) ?>">
    <label>Username
        <input name="username" required autocomplete="username">
    </label>
    <label>Password
        <input type="password" name="password" required autocomplete="current-password">
    </label>
    <button type="submit">Accedi</button>
</form>
