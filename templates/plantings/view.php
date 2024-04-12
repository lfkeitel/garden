<?php $this->layout('main', ['title' => "Planting = {$planting->display_string()}"]) ?>

<h2>== Planting of <?= $planting->seed->common_name ?> - <?= $planting->seed->variety ?> ==</h2>

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
            <dd><?= $planting->date->format('Y-m-d') ?> (<?= $this->days_from_date($planting->date) ?>)</dd>

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
                <i><?= $this->date_plus_days($planting->date, $planting->seed->days_to_maturity) ?></i>
            </dd>

            <dt>Is Transplant?:</dt>
            <dd><?= count($planting->transplant_log) > 0 || $planting->is_transplant ? 'Yes' : 'No' ?></dd>

            <dt>Tags:</dt>
            <dd><?= count($planting->tags) == 0 ? 'None' : implode(", ", $planting->tags) ?></dd>
        </dl>

        <h3>Location</h3>
        <dl>
            <dt>Bed:</dt>
            <dd><?= $planting->bed->name ?></dd>

            <dt>Row:</dt>
            <dd><?= $planting->row ?></dd>

            <dt>Column:</dt>
            <dd><?= $planting->column ?></dd>

            <dt>Tray ID:</dt>
            <dd><?= $planting->tray_id ?: 'Not in a tray' ?></dd>
        </dl>

        <h3>Notes</h3>
        <p class="notes"><?= $planting->notes ?></p>
    </article>

    <section>
        <h3>Transplant Log</h3>
        <?php foreach ($planting->transplant_log as $log) : ?>
            <p>
                <?= $log->date->format('Y-m-d') ?> <strong>From:</strong> <?= $log->from->display_string() ?> -> <strong>To:</strong> <?= $log->to->display_string() ?>
            </p>
        <?php endforeach ?>

        <?php $this->insert(
            'partials::log-table',
            [
                'logs' => $logs,
                'planting_id' => $planting->get_id(),
            ]
        ) ?>
    </section>
</div>
