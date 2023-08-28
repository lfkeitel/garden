<?php $this->layout('main', ['title' => 'Logs']) ?>

<?php $this->insert('partials::log-table', ['logs' => $all_logs]) ?>
