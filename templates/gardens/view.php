<?php $this->layout('main', ['title' => "Garden = {$garden->display_string()}"]) ?>

<h2>== Garden <?= $garden->name ?> ==</h2>

<p>
    <?php if ($this->is_logged_in()): ?>
    <a href="<?= $basepath ?>/gardens/edit/<?= $garden->get_id() ?>" class="btn">Edit</a>
    <?php endif ?>
</p>

<article>
    <h3>Properties</h3>
    <dl>
        <dt>Added:</dt>
        <dd><?= $garden->added->format('Y-m-d') ?></dd>

        <dt>Name:</dt>
        <dd><?= $garden->name ?></dd>

        <dt>Rows:</dt>
        <dd><?= $garden->rows ?></dd>

        <dt>Columns:</dt>
        <dd><?= $garden->cols ?></dd>

        <dt>Total ft<sup>2</sup>:</dt>
        <dd><?= $garden->rows * $garden->cols ?> ft<sup>2</sup></dd>

        <dt>Hidden from Home:</dt>
        <dd><?= $garden->hide_from_home ? 'Yes' : 'No' ?></dd>
    </dl>

    <h3>Notes</h3>
    <p class="notes"><?= $garden->notes ?></p>

    <h3>Beds in Garden (<?= count($beds) ?>)</h3>

    <?php if ($this->is_logged_in()) : ?>
    <a href="<?= $basepath ?>/beds/new" class="btn">New Bed</a>
    <?php endif ?>

    <table class="seed-table">
        <thead>
            <tr>
                <th scope="col">Name</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($beds as $bed) : ?>
            <tr>
                <td>
                    <a href="<?= $basepath ?>/bed/<?= $bed->get_id() ?>"><?= $bed->display_string() ?></a>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</article>