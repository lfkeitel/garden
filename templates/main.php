<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=Edge">
        <meta name="viewport" content="width=device-width, initial-scale=0.75">

        <?php if (isset($title) && $title != ""): ?>
        <title>Lee's Garden : <?= $title ?></title>
        <?php else: ?>
        <title>Lee's Garden</title>
        <?php endif ?>

        <link rel="stylesheet" href="<?= $basepath ?>/static/styles/main.css">
        <?php if(isset($styles)): foreach($styles as $style): ?>
        <link rel="stylesheet" href="<?= $basepath ?>/static/styles/<?= $style ?>.css">
        <?php endforeach; endif ?>
    </head>

    <body>
        <div class="sidebar">
            <nav>
                <ul>
                    <li><a href="<?= $basepath ?>/" class="<?= $app->request->REQUEST_URI == "/" ? 'active-page' : '' ?>">Home</a></li>
                    <li><a href="<?= $basepath ?>/logs" class="<?= $app->request->REQUEST_URI == "/logs" ? 'active-page' : '' ?>">Log</a></li>
                    <li><a href="<?= $basepath ?>/plantings?filter=Active" class="<?= $app->request->REQUEST_URI == "/plantings" ? 'active-page' : '' ?>">Plantings</a></li>
                    <li><a href="<?= $basepath ?>/seeds" class="<?= $app->request->REQUEST_URI == "/seeds" ? 'active-page' : '' ?>">Seeds</a></li>
                    <li><a href="<?= $basepath ?>/wishlist" class="<?= $app->request->REQUEST_URI == "/wishlist" ? 'active-page' : '' ?>">Wishlist</a></li>
                    <li><a href="<?= $basepath ?>/beds" class="<?= $app->request->REQUEST_URI == "/beds" ? 'active-page' : '' ?>">Beds</a></li>
                    <?php if ($this->is_logged_in()): ?>
                    <li><a href="/logout">Logout</a></li>
                    <?php else: ?>
                    <li><a href="/login" class="<?= $app->request->REQUEST_URI == "/login" ? 'active-page' : '' ?>">Login</a></li>
                    <?php endif ?>
                </ul>
            </nav>
        </div>

        <div class="content">
            <?php if (isset($toast) && $toast != ""): ?>
            <div class="toast"><?= $toast ?></div>
            <?php endif ?>

            <div class="main-content">
            <?= $this->section('content') ?>
            </div>
        </div>
    </body>

    <script type="text/javascript" src="<?= $basepath ?>/static/scripts/common.js"></script>
    <?php if(isset($scripts)): foreach($scripts as $script): ?>
    <script type="text/javascript" src="<?= $basepath ?>/static/scripts/<?= $script ?>.js"></script>
    <?php endforeach; endif ?>
</html>
