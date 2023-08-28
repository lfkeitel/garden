<?php $this->layout('main', ['title' => "Planting Photo Gallery"]) ?>

<h2>== Photo Gallery of <?= $planting->seed->common_name ?> - <?= $planting->seed->variety ?> ==</h2>

<p>
    <a href="/plantings/gallery/<?= $this->e($planting->get_id()) ?>?dir=<?= $sort_dir ?>" class="btn">Sort</a>
</p>

<article class="gallery">
    <?php foreach ($logs as $log): ?>
        <?php foreach ($log->image_files as $file): ?>
            <p class="gallery-item">
                <?= $log->date->format('Y-m-d') ?><br>
                <a href="/uploads/<?= $file ?>" target="_blank">
                    <img src="/uploads/<?= $file ?>" width="360" height="270">
                </a>
            </p>
        <?php endforeach ?>
    <?php endforeach ?>
</article>
