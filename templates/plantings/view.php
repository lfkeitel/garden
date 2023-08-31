<?php $this->layout('main', ['title' => "Planting = {$planting->display_string()}"]) ?>

<h2>== Planting of <?= $planting->seed->common_name ?> - <?= $planting->seed->variety ?> ==</h2>

<p>
    <a href="/plantings/edit/<?= $planting->get_id() ?>" class="btn">Edit</a>
    <a href="/plantings/gallery/<?= $this->e($planting->get_id()) ?>" class="btn">Photo Gallery</a>
</p>

<div class="side-by-side">
    <article>
        <h3>Information</h3>
        <dl>
            <dt>Planted:</dt>
            <dd><?= $planting->date->format('Y-m-d') ?> (<?= $this->days_from_date($planting->date) ?>)</dd>

            <dt>Seed:</dt>
            <dd>
                <a href="/seeds/<?= $planting->seed->get_id() ?>">
                    <?= $planting->seed->display_string() ?>
                </a>
            </dd>

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
            <dd><?= $planting->is_transplant ? 'Yes': 'No' ?></dd>
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
        <?php $this->insert('partials::log-table',
            [
                'logs' => $logs,
                'planting_id' => $planting->get_id(),
            ]) ?>
    </section>
</div>
