<?php $this->layout(
    'main',
    [
        'scripts' => ['edit-log'],
        'title' => "Edit Log = {$log->display_string()}"
    ]
) ?>

<h2>Update Log</h2>

<form method="POST" id="log-form">
    <fieldset>
        <legend>Properties</legend>

        <p>
            <label>
                Planting:
                <select name="planting">
                    <option <?= is_null($log->planting) ? 'selected' : '' ?>>All</option>
                    <?php foreach ($plantings as $planting) : ?>
                        <option value="<?= $planting['id'] ?>" <?= $log->planting && $log->planting->get_id() === $planting['id'] ? 'selected' : '' ?>><?= $planting['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>

        <p>
            <label>
                Planting Tag:
                <select name="planting_tag">
                    <option <?= $log->planting_tag === '' ? 'selected' : '' ?> value="">None</option>
                    <?php foreach ($planting_tags as $tag) : ?>
                        <option <?= $log->planting_tag === $tag ? 'selected' : '' ?>><?= $tag ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>

        <p>
            <label>
                Time of Day:
                <select name="time_of_day">
                    <option <?= $log->time_of_day === 'Morning' ? 'selected' : '' ?>>Morning</option>
                    <option <?= $log->time_of_day === 'Afternoon' ? 'selected' : '' ?>>Afternoon</option>
                    <option <?= $log->time_of_day === 'Evening' ? 'selected' : '' ?>>Evening</option>
                </select>
            </label>
        </p>

        <p>
            <label>
                Notes:<br>
                <textarea cols="50" rows="6" name="notes"><?= $log->notes ?></textarea>
            </label>
        </p>
    </fieldset>

    <?php if (count($log->image_files) > 0) : ?>
        <fieldset>
            <input type="hidden" id="image_files" name="image_files" value="<?= implode(';', $log->image_files) ?>">

            <?php foreach ($log->image_files as $file) : ?>
                <p><img src="<?= $basepath ?>/uploads/<?= $file ?>"></p>
            <?php endforeach ?>
        </fieldset>
    <?php endif ?>

    <button type="submit">Update Log</button>
</form>
