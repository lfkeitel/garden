<?php $this->layout('main', ['title' => "Planting = {$planting->display_string()}"]) ?>

<h2>== Planting of <?= $planting->seed->common_name ?> -
    <?= $planting->seed->variety ?> ==
</h2>

<p>
    <?php if ($this->is_logged_in()) : ?>
    <a href="<?= $basepath ?>/plantings/edit/<?= $planting->get_id() ?>" class="btn">Edit</a>
    <a href="<?= $basepath ?>/plantings/transplant/<?= $planting->get_id() ?>" class="btn">Transplant</a>
    <?php endif ?>
    <a href="<?= $basepath ?>/plantings/gallery/<?= $this->e($planting->get_id()) ?>" class="btn">Photo Gallery</a>
</p>

<div class="side-by-side">
    <article>
        <h3>Information</h3>
        <dl>
            <dt>Planted:</dt>
            <dd><?= $planting->date->format('Y-m-d') ?>
                (<?= $this->days_from_date($planting->date, $planting->harvest_date) ?>)
            </dd>

            <dt>Sprouted:</dt>
            <dd><?= $planting->sprout_date ? $planting->sprout_date->format('Y-m-d') . ' ('. $this->days_from_date($planting->sprout_date) . ')' : "Not yet" ?>
            </dd>

            <dt>Germination:</dt>
            <dd><?= $planting->sprout_date ? $planting->date->diff($planting->sprout_date, true)->d . ' days' : "Not yet" ?>
            </dd>

            <dt>Seed:</dt>
            <dd>
                <a href="<?= $basepath ?>/seeds/<?= $planting->seed->get_id() ?>">
                    <?= $planting->seed->display_string() ?>
                </a>
            </dd>

            <dt>Plant Count:</dt>
            <dd><?= $planting->count ?></dd>

            <dt>Status:</dt>
            <dd><?= $planting->status ?></dd>

            <dt>Harvest Date</dt>
            <dd>
                <?= is_null($planting->harvest_date) ? 'Still growing' : $planting->harvest_date->format('Y-m-d') ?>
            </dd>

            <dt>Expected Maturity</dt>
            <dd>
                <i><?= $this->plant_maturity_day($planting) ?></i>
            </dd>

            <dt>Is Transplant?:</dt>
            <dd><?= count($planting->transplant_log) > 0 || $planting->is_transplant ? 'Yes' : 'No' ?>
            </dd>

            <dt>Tags:</dt>
            <dd><?= $planting->tags_to_str() ?>
            </dd>
        </dl>

        <h3>Location</h3>
        <dl>
            <dt>Bed:</dt>
            <dd>
                <?php if ($planting->bed): ?>
                <a href="<?= $basepath ?>/beds/<?= $planting->bed->get_id() ?>">
                    <?= $planting->bed->name ?>
                </a>
                <?php else: ?>
                    Deleted bed
                <?php endif ?>
            </dd>

            <dt>Row:</dt>
            <dd><?= $planting->row ?></dd>

            <dt>Column:</dt>
            <dd><?= $planting->column ?></dd>

            <dt>Tray ID:</dt>
            <dd><?= $planting->tray_id ?: 'Not in a tray' ?>
            </dd>
        </dl>

        <h3>Notes</h3>
        <p class="notes"><?= $planting->notes ?></p>
    </article>

    <section>
        <h3>Transplant Log</h3>
        <?php foreach ($planting->transplant_log as $log) : ?>
        <p>
            <?= $log->date->format('Y-m-d') ?>
            <strong>From:</strong>
            <?= $log->from->display_string() ?> ->
            <strong>To:</strong> <?= $log->to->display_string() ?>
        </p>
        <?php endforeach ?>
        <?php if (count($planting->transplant_log) === 0) : ?>
        <p>
            No transplant logs.
        </p>
        <?php endif ?>

        <h3>Planting Log</h3>
        <?php $this->insert(
            'partials::log-table',
            [
                'logs' => $logs,
                'planting' => $planting,
            ]
        ) ?>
    </section>
</div>