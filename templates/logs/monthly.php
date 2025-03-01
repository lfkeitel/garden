<?php $this->layout('main', ['title' => "Logs Monthly View"]) ?>

<h2>== Garden Logs Monthly View - <?= count($items) ?> Logs ==</h2>

<?php $this->insert(
    'partials::calendar-controls',
    [
        'month' => $month,
    ]
) ?>

<?php $this->insert(
    'partials::calendar-table',
    [
        'items' => $items,
        'month' => $month,
        'start_day' => $start_day,
    ]
) ?>
