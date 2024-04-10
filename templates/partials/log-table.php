<?php $edit_btns = $edit_btns ?? true; ?>

<?php if ($this->is_logged_in()) : ?>
    <?php if (isset($planting_id) && $planting_id !== '') : ?>
        <a href="<?= $basepath ?>/logs/new?planting=<?= $this->e($planting_id) ?>" class="btn">New Log</a>
    <?php else : ?>
        <a href="<?= $basepath ?>/logs/new" class="btn">New Log</a>
    <?php endif ?>
<?php endif ?>

<table class="seed-table">
    <thead>
        <tr>
            <?php if ($this->is_logged_in() && $edit_btns) : ?>
                <th scope="col"></th>
            <?php endif ?>
            <th scope="col"></th>
            <th scope="col">Date</th>
            <th scope="col">Weather</th>
            <?php if (!isset($planting_id)) : ?><th scope="col">Planting</th><?php endif ?>
            <th scope="col">Time of Day</th>
            <th scope="col">Notes</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($logs as $log) : ?>
            <tr>
                <?php if ($this->is_logged_in() && $edit_btns) : ?>
                    <td class="control-cell">
                        <form method="get" action="/logs/edit/<?= $this->e($log->get_id()) ?>">
                            <button type="submit" class="btn btn-small">Edit</button>
                        </form>

                        <form method="post" onsubmit="return form_confirm(this);">
                            <input type="hidden" value="delete_log" name="action">
                            <input type="hidden" value="<?= $log->get_id() ?>" name="log_id">
                            <button type="submit" class="btn btn-small">Delete</button>
                        </form>
                    </td>
                <?php endif ?>
                <td><a href="<?= $basepath ?>/logs/<?= $log->get_id() ?>">View Log</a></td>
                <td><?= $log->date->format('Y-m-d') ?></td>
                <td><?= $log->weather->temp_high ?>/<?= $log->weather->temp_low ?>&deg;C</td>
                <?php if (!isset($planting_id)) : ?>
                    <?php if (!is_null($log->planting)) : ?>
                        <td><a href="<?= $basepath ?>/plantings/<?= $log->planting->get_id() ?>"><?= $log->display_string() ?></a></td>
                    <?php else : ?>
                        <td><?= $log->display_string() ?></td>
                    <?php endif ?>
                <?php endif ?>
                <td><?= $log->time_of_day ?></td>
                <td><?= count($log->image_files) > 0 ? "<a href=\"{$basepath}/logs/{$log->get_id()}#images\">📷</a> " : '' ?><?= nl2br($log->notes) ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>
