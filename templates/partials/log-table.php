<?php $edit_btns = $edit_btns ?? true; ?>

<?php if ($this->is_logged_in()) : ?>
<?php if (isset($planting) && $planting !== '') : ?>
<a href="<?= $basepath ?>/logs/new?planting=<?= $this->e($planting->get_id()) ?>" class="btn">New Log</a>
<?php else : ?>
<a href="<?= $basepath ?>/logs/new" class="btn">New Log</a>
<?php endif ?>
<?php endif ?>

<table class="seed-table">
    <thead>
        <tr>
            <th scope="col"></th>
            <th scope="col">Date</th>
            <th scope="col">Weather</th>
            <?php if (!isset($planting)) : ?><th scope="col">Planting</th><?php endif ?>
            <th scope="col">Time of Day</th>
            <th scope="col">Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log) : ?>
        <tr>
            <td>
                <div class="control-cell">
                    <a class="btn btn-small" href="<?= $basepath ?>/logs/<?= $log->get_id() ?>">Open</a>
                    <?php if ($this->is_logged_in() && $edit_btns) : ?>

                    <form method="get" action="/logs/edit/<?= $this->e($log->get_id()) ?>">
                        <button type="submit" class="btn btn-small">Edit</button>
                    </form>

                    <form method="post" onsubmit="return form_confirm(this);">
                        <input type="hidden" value="delete_log" name="action">
                        <input type="hidden" value="<?= $log->get_id() ?>" name="log_id">
                        <button type="submit" class="btn btn-small">Delete</button>
                    </form>
                    <?php endif ?>
                </div>
            </td>

            <?php if (!is_null($log->planting)) : ?>
            <td><?= $log->date->format('Y-m-d') ?> (<?= $this->days_from_date($log->planting->date, $log->date) ?>)</td>
            <?php elseif (isset($planting)) : ?>
            <td><?= $log->date->format('Y-m-d') ?> (<?= $this->days_from_date($planting->date, $log->date) ?>)
            </td>
            <?php else : ?>
            <td><?= $log->date->format('Y-m-d') ?> </td>
            <?php endif ?>

            <td><?= $log->weather->temp_high ?>/<?= $log->weather->temp_low ?>&deg;C</td>
            <?php if (!isset($planting)) : ?>
            <?php if (!is_null($log->planting)) : ?>
            <td><a href="<?= $basepath ?>/plantings/<?= $log->planting->get_id() ?>"><?= $log->display_string() ?></a>
            </td>
            <?php elseif ($log->planting_tag) : ?>
            <td>Tag: <a href="/plantings?tag=<?= $log->planting_tag ?>"><?= $log->planting_tag ?></a></td>
            <?php else : ?>
            <td><?= $log->display_string() ?></td>
            <?php endif ?>
            <?php endif ?>
            <td><?= $log->time_of_day ?></td>
            <td><?= count($log->image_files) > 0 ? "<a href=\"{$basepath}/logs/{$log->get_id()}#images\">📷</a> " : '' ?><?= nl2br($log->notes) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>