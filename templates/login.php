<?php $this->layout('main') ?>

<?php if ($this->is_logged_in()): ?>
<p>
    You're already logged in!
</p>
<?php else: ?>
<form action="<?= $basepath ?>/login" method="post">
    Username: <input type="text" name="username" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" autofocus><br>
    Password: <input type="password" name="password"><br><br>

    <button type="submit">Login</button>
</form>
<?php endif ?>
