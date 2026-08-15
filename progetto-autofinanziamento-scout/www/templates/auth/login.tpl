<?php $this->layout('layouts/main', ['title' => 'Accesso amministratore']) ?>

<h1>Accesso amministratore</h1>
<p>Accedi per gestire i prodotti e il magazzino.</p>

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
