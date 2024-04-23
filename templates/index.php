<?php
$this->layout('main', ['title' => "Home"]);
$now = new DateTimeImmutable();
?>

<h2>Season Information (Zone <?= $usda_zone ?> / <?= $season_length ?> day season)</h2>

<p>
    <strong>Today's Date:</strong> <?= date('Y-m-d') ?>
</p>
<p>
    <strong>Days to First Frost:</strong>
    <?= $now->diff($first_frost)->format('%a') ?>
    (~<?= round(\intval($now->diff($first_frost)->format('%a')) / 7) ?> weeks)
    (<?= $first_frost->format('Y-m-d') ?>)
</p>
<p>
    <strong>Days to Last Frost:</strong>
    <?= $now->diff($last_frost)->format('%a') ?>
    (<?= $last_frost->format('Y-m-d') ?>)
</p>

<h2>Bed Status</h2>

<?php if ($this->is_logged_in()) : ?>
<a href="<?= $basepath ?>/beds/new" class="btn">New Bed</a>
<?php endif ?>

<table class="seed-table">
    <thead>
        <tr>
            <th scope="col">Bed Name</th>
            <th scope="col">Plantings</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($beds as $bed) : ?>
        <tr>
            <td><a href="<?= $basepath ?>/beds/<?= $bed->get_id() ?>"><?= $bed->display_string() ?></a></td>

            <td>
                <table class="no-color-bg planting-table">
                    <?php for ($i = 1; $i <= $bed->rows; $i++) : ?>
                    <tr>
                        <?php for ($j = 1; $j <= $bed->cols; $j++) : ?>
                        <td>
                            <?php $n = 0;
                                    foreach ($bed_plantings[$bed->get_id()] as $planting) : ?>
                            <?php if ($planting->row === $i && $planting->column === $j) : ?>

                            <div class="planting-cell <?= $this->planting_status_class($planting) ?>">
                                <a href="<?= $basepath ?>/plantings/<?= $planting->get_id() ?>"
                                    title="<?= $planting->notes ?>">
                                    <?= $planting->count ?> x <?= $planting->display_string() ?>
                                    (<?= $planting->row ?>/<?= $planting->column ?>)
                                    (<?= $this->days_from_date($planting->date) ?>) (<?= $planting->status ?>)<br>
                                </a>
                            </div>

                            <?php $n++;
                                            endif ?>
                            <?php endforeach ?>

                            <?php if ($n === 0) : ?>
                            <div class="planting-cell planting-none">
                                Bed is empty. <?php if ($this->is_logged_in()) : ?>(<a
                                    href="/plantings/new?col=<?= $j ?>&row=<?= $i ?>&bed=<?= $bed->get_id() ?>">Create</a>)<?php endif ?>

                            </div>
                            <?php endif ?>
                        </td>
                        <?php endfor ?>
                    </tr>
                    <?php endfor ?>
                </table>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<h2>Latest Logs</h2>

<?php $this->insert('partials::log-table', ['logs' => $logs, 'edit_btns' => false]) ?>

<h2>Current Plantings (<?= count($plantings) ?>)</h2>

<?php if ($this->is_logged_in()) : ?>
<a href="<?= $basepath ?>/plantings/new" class="btn">New Planting</a>
<?php endif ?>

<table class="seed-table">
    <thead>
        <tr>
            <th scope="col">Planted</th>
            <th scope="col">Seed</th>
            <th scope="col">Count</th>
            <th scope="col">Bed</th>
            <th scope="col">Status</th>
            <th scope="col">Tags</th>
            <th scope="col">Maturity Date</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($plantings as $planting) : ?>
        <tr>
            <td><?= $planting->date->format('Y-m-d') ?></td>
            <td><a href="<?= $basepath ?>/plantings/<?= $planting->get_id() ?>"><?= $planting->display_string() ?></a>
            </td>
            <td><?= $planting->count ?></td>
            <td><?= $planting->bed->name ?> (<?= $planting->row ?>/<?= $planting->column ?>)</td>
            <td><?= $planting->status ?></td>
            <td><?= $planting->tags_to_str() ?></td>
            <td><?= is_null($planting->harvest_date) ? '<i>' . $this->date_plus_days($planting->date, $planting->seed->days_to_maturity) . '*</i>' : $planting->harvest_date->format('Y-m-d') ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<p>
    * Estimated maturity date.
</p>
