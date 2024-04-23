<?php $this->layout('main', ['title' => "Bed = {$bed->display_string()}"]) ?>

<h2>== Bed <?= $bed->name ?> ==</h2>

<p>
    <?php if ($this->is_logged_in()): ?>
    <a href="<?= $basepath ?>/beds/edit/<?= $bed->get_id() ?>" class="btn">Edit</a>
    <a href="<?= $basepath ?>/plantings/new?bed=<?= $bed->get_id() ?>" class="btn">New Planting</a>
    <?php endif ?>
</p>

<article>
    <h3>Properties</h3>
    <dl>
        <dt>Added:</dt>
        <dd><?= $bed->added->format('Y-m-d') ?></dd>

        <dt>Name:</dt>
        <dd><?= $bed->name ?></dd>

        <dt>Rows:</dt>
        <dd><?= $bed->rows ?></dd>

        <dt>Columns:</dt>
        <dd><?= $bed->cols ?></dd>

        <dt>Total ft<sup>2</sup>:</dt>
        <dd><?= $bed->rows * $bed->cols ?> ft<sup>2</sup></dd>

        <dt>Hidden from Home:</dt>
        <dd><?= $bed->hide_from_home ? 'Yes' : 'No' ?></dd>
    </dl>

    <h3>Notes</h3>
    <p class="notes"><?= $bed->notes ?></p>

    <h3>Current Plantings (<?= count($plantings) ?>)</h3>

    <?php if ($this->is_logged_in()) : ?>
    <a href="<?= $basepath ?>/plantings/new?bed=<?= $bed->get_id() ?>" class="btn">New Planting</a>
    <?php endif ?>

    <table class="seed-table">
        <thead>
            <tr>
                <th scope="col">Planted</th>
                <th scope="col">Seed</th>
                <th scope="col">Status</th>
                <th scope="col">Tags</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plantings as $planting) : ?>
            <tr>
                <td><?= $planting->date->format('Y-m-d') ?></td>
                <td><a href="<?= $basepath ?>/plantings/<?= $planting->get_id() ?>"><?= $planting->display_string() ?></a>
                </td>
                <td><?= $planting->status ?></td>
                <td><?= $planting->tags_to_str() ?></td>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</article>
