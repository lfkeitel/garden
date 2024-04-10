<?php $this->layout('main', ['styles' => ['login']]) ?>

<?php if ($this->is_logged_in()) : ?>
    <p>
        You're already logged in!
    </p>
<?php else : ?>
    <form class="login-form" action="<?= $basepath ?>/login" method="post">
        <label>
            <span>Username:</span>
            <input type="text" name="username" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" autofocus>
        </label>

        <label>
            <span>Password:</span>
            <input type="password" name="password">
        </label>

        <button type="submit">Login</button>
    </form>
<?php endif ?>
