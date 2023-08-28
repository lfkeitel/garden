<?php $this->layout('main', ['title' => "Log = {$log->display_string()}"]) ?>

<h2>== Log <?= $log->date->format('Y-m-d H:i:s') ?> ==</h2>

<p>
    <a href="/logs/edit/<?= $log->get_id() ?>" class="btn">Edit</a>
</p>

<article>
    <h3>Properties</h3>
    <dl>
        <dt>Date:</dt>
        <dd><?= $log->date->format('Y-m-d H:i:s') ?></dd>

        <dt>Planting:</dt>
        <dd>
            <?php if ($log->planting): ?>
            <a href="/plantings/<?= $log->planting->get_id() ?>">
            <?= $log->planting->seed->common_name.' - '.$log->planting->seed->variety ?>
            </a>
            <?php else: ?>
                All
            <?php endif ?>
        </dd>

        <dt>Time of Day:</dt>
        <dd><?= $log->time_of_day ?></dd>
    </dl>

    <h3>Notes</h3>
    <p class="notes"><?= nl2br($log->notes) ?></p>

    <h3>Images</h3>
    <?php foreach ($log->image_files as $file): ?>
    <p><a href="/uploads/<?= $file ?>" target="_blank"><img src="/uploads/<?= $file ?>" width="720" height="540"></a></p>
    <?php endforeach ?>
</article>
