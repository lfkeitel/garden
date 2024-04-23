<?php $this->layout(
    'main',
    ['scripts' => ['seed-form'],
     'title' => "Seeds = {$seed->display_string()}"]
) ?>

<h2>== <?= $seed->common_name ?> - <?= $seed->variety ?> <?= $seed->on_wishlist ? '(Wishlist)' : '' ?> ==</h2>

<p>
    <?php if ($this->is_logged_in()): ?>
    <a href="<?= $basepath ?>/seeds/edit/<?= $seed->get_id() ?>" class="btn">Edit</a>
    <?php endif ?>
</p>

<article>
    <h3>Plant Identification</h3>
    <dl>
        <dt>Type:</dt>
        <dd><?= $seed->type ?></dd>

        <dt>Common Name:</dt>
        <dd><?= $seed->common_name ?></dd>

        <dt>Variety:</dt>
        <dd><?= $seed->variety ?></dd>
    </dl>

    <h3>Growth Properties</h3>
    <dl>
        <dt>Days to Maturity:</dt>
        <dd><?= $seed->days_to_maturity ?>  (<i><?= $this->date_plus_days((new DateTimeImmutable()), $seed->days_to_maturity) ?></i>)</dd>

        <dt>Days to Germination:</dt>
        <dd><?= $seed->days_to_germination ?></dd>

        <dt>Average Germination Time:</dt>
        <dd><?= $avg_germ_time === 0 ? 'Unknown' : $avg_germ_time ?> days</dd>

        <dt>Is Heirloom:</dt>
        <dd><?= $seed->is_heirloom ? 'Yes' : 'No' ?></dd>

        <dt>Is Hybrid:</dt>
        <dd><?= $seed->is_hybrid ? 'Yes' : 'No' ?></dd>

        <dt>Sun:</dt>
        <dd><?= $seed->sun ?></dd>

        <dt>Season:</dt>
        <dd><?= implode(", ", $seed->season) ?></dd>

        <dt>Characteristics:</dt>
        <dd><?= count($seed->characteristics) == 0 ? 'None' : implode(", ", $seed->characteristics) ?></dd>
    </dl>

    <h3>Other Information</h3>
    <dl>
        <dt>Source:</dt>
        <?php if ($seed->link !== ''): ?>
        <dd><a href="<?= $seed->link ?>" target="_blank"><?= $seed->source ?></a></dd>
        <?php else: ?>
        <dd><?= $seed->source ?></dd>
        <?php endif ?>

        <dt>Added:</dt>
        <dd><?= $seed->added->format("Y-m-d H:i:s") ?></dd>

        <dt>Tags:</dt>
        <dd><?= count($seed->tags) == 0 ? 'None' : implode(", ", $seed->tags) ?></dd>
    </dl>

    <h3>Notes</h3>
    <p class="notes"><?= $seed->notes ?></p>

    <h3>Current Plantings (<?= count($plantings) ?>)</h3>

    <?php if ($this->is_logged_in()) : ?>
    <a href="<?= $basepath ?>/plantings/new?seed=<?= $seed->get_id() ?>" class="btn">New Planting</a>
    <?php endif ?>

    <table class="seed-table">
        <thead>
            <tr>
                <th scope="col">Planted</th>
                <th scope="col">Bed</th>
                <th scope="col">Status</th>
                <th scope="col">Tags</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($plantings as $planting) : ?>
            <tr>
                <td><a href="<?= $basepath ?>/plantings/<?= $planting->get_id() ?>"><?= $planting->date->format('Y-m-d') ?></a></td>
                <td><?= $planting->bed->name ?> (<?= $planting->row ?>/<?= $planting->column ?>)</td>
                <td><?= $planting->status ?></td>
                <td><?= $planting->tags_to_str() ?></td>
                </td>
            </tr>
            <?php endforeach ?>
        </tbody>
    </table>
</article>
