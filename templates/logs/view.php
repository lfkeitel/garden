<?php $this->layout('main', ['title' => "Log = {$log->display_string()}"]) ?>

<h2>== Log <?= $log->date->format('Y-m-d H:i:s') ?> ==</h2>

<p>
    <?php if ($this->is_logged_in()): ?>
    <a href="<?= $basepath ?>/logs/edit/<?= $log->get_id() ?>" class="btn">Edit</a>
    <?php endif ?>
</p>

<article>
    <h3>Properties</h3>
    <dl>
        <dt>Date:</dt>
        <dd><?= $log->date->format('Y-m-d H:i:s') ?></dd>

        <dt>Planting:</dt>
        <dd>
            <?php if ($log->planting): ?>
            <a href="<?= $basepath ?>/plantings/<?= $log->planting->get_id() ?>">
            <?= $log->planting->seed->common_name.' - '.$log->planting->seed->variety ?>
            </a>
            <?php else: ?>
                All
            <?php endif ?>
        </dd>

        <dt>Time of Day:</dt>
        <dd><?= $log->time_of_day ?></dd>
    </dl>

    <h3>Weather</h3>
    <dl>
        <dt>Day High</dt>
        <dd><?= $log->weather->temp_high ?>&deg;C</dd>

        <dt>Day Low</dt>
        <dd><?= $log->weather->temp_low ?>&deg;C</dd>

        <dt>Afternoon</dt>
        <dd><?= $log->weather->temp_afternoon ?>&deg;C</dd>

        <dt>Night</dt>
        <dd><?= $log->weather->temp_night ?>&deg;C</dd>

        <dt>Evening</dt>
        <dd><?= $log->weather->temp_evening ?>&deg;C</dd>

        <dt>Morning</dt>
        <dd><?= $log->weather->temp_morning ?>&deg;C</dd>

        <dt>Precipitation</dt>
        <dd><?= $log->weather->precipitation ?>in</dd>

        <dt>Cloud Cover</dt>
        <dd><?= $log->weather->cloud_cov ?>%</dd>

        <dt>Humidity</dt>
        <dd><?= $log->weather->humidity ?>%</dd>
    </dl>

    <h3>Notes</h3>
    <p class="notes"><?= nl2br($log->notes) ?></p>

    <h3 id="images">Images</h3>
    <?php foreach ($log->image_files as $file): ?>
    <p><a href="<?= $basepath ?>/uploads/<?= $file ?>" target="_blank"><img src="<?= $basepath ?>/uploads/<?= $file ?>" width="720" height="540"></a></p>
    <?php endforeach ?>
</article>
