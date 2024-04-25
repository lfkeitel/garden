<?php $this->layout('main', ['title' => 'Logs']) ?>

<a href="/logs/gallery" class="btn">Photo Gallery</a>

<?php $this->insert('partials::log-table', ['logs' => $all_logs]) ?>
