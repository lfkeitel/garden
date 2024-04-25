<?php $this->layout('main', ['title' => "Photo Gallary"]) ?>

<h2>== Garden Photo Gallary - <?= count($logs) ?> Logs ==</h2>

<form>
    <label>
        Start Date: <input type="date" name="start_date" value="<?= $start_date ?>">
    </label>

    <label>
        End Date: <input type="date" name="end_date" value="<?= $end_date ?>">
    </label>

    <button type="submit">Search</button>
</form>

<article class="gallery">
    <?php foreach ($logs as $log): ?>
        <?php foreach ($log->image_files as $file): ?>
            <p class="gallery-item">
                <span title="<?= $log->notes ?>">
                    <?= $log->notes !== '' ? '&diams;' : '' ?>
                    <a href="<?= $basepath ?>/logs/<?= $log->get_id() ?>" target="_blank"><?= $log->date->format('Y-m-d') ?></a>
                </span><br>
                <a href="<?= $basepath ?>/uploads/<?= $file ?>" target="_blank">
                    <img src="<?= $basepath ?>/uploads/<?= $file ?>" width="360" height="270">
                </a>
            </p>
        <?php endforeach ?>
    <?php endforeach ?>
</article>
