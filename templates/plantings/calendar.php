<?php $this->layout('main', ['title' => "Planting Calendar"]) ?>

<?php $this->insert(
    'partials::calendar-controls',
    [
        'month' => $month,
    ]
) ?>

<?php $this->insert(
    'partials::calendar-table',
    [
        'items' => $plantings,
        'month' => $month,
        'start_day' => $start_day,
    ]
) ?>
