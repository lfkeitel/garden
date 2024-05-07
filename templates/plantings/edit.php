<?php $this->layout(
    'main',
    [
        'scripts' => ['seed-form'],
        'title' => "Edit Planting = {$planting->display_string()}"
    ]
) ?>

<h2>Update Planting</h2>

<form method="POST" id="planting-form">
    <fieldset>
        <legend>Crop</legend>

        <p>
            <label>
                Seed:

                <select name="seed">
                    <?php foreach ($seeds as $seed) : ?>
                        <option value="<?= $seed['id'] ?>" <?= $planting->seed->get_id() == $seed['id'] ? 'selected' : '' ?>><?= $seed['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>

        <p>
            <label>
                Parent Planting:

                <select name="parent">
                    <option value="">None</a>
                    <?php foreach ($all_plantings as $parent) : ?>
                        <?php if ($parent->get_id() === $planting->get_id()) { continue; } ?>

                        <option value="<?= $parent->get_id() ?>" <?= $planting->parent && $parent->get_id() === $planting->parent->get_id() ? 'selected' : '' ?>><?= $parent->date->format("Y-m-d") ?> <?= $parent->display_string_with_bed() ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>

        <p>
            <label>
                Plant Count: <input type="number" name="count" value="<?= $planting->count ?>">
            </label>
        </p>

        <p>
            <label>
                Status:

                <select name="status">
                    <?php foreach ($planting_statuses as $status) : ?>
                        <option <?= $planting->status === $status ? 'selected' : '' ?>><?= $status ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>
    </fieldset>

    <fieldset>
        <legend>Planting Location</legend>

        <p>
            <label>
                Bed:
                <select name="bed">
                    <?php foreach ($beds as $bed) : ?>
                        <option value="<?= $bed['id'] ?>" <?= $planting->bed->get_id() == $bed['id'] ? 'selected' : '' ?>><?= $bed['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                Row: <input type="number" name="row" value="<?= $planting->row ?>">
            </label>
        </p>
        <p>
            <label>
                Column: <input type="number" name="column" value="<?= $planting->column ?>">
            </label>
        </p>
        <p>
            <label>
                Tray ID: <input type="text" name="tray_id" value="<?= $planting->tray_id ?>" placeholder="Tray #-Cell block">
            </label>
        </p>
    </fieldset>

    <fieldset>
        <legend>Other Information</legend>

        <p>
            <label>
                Planting Date: <input type="date" name="planting_date" value="<?= $planting->date->format("Y-m-d") ?>">
            </label>
        </p>

        <p>
            <label>
                Sprouting Date: <input type="date" name="sprouting_date" value="<?= $planting->sprout_date ? $planting->sprout_date->format("Y-m-d") : '' ?>">
            </label>
        </p>

        <p>
            <label>
                Is transplant?

                <input type="radio" name="is_transplant" value="Yes" <?= $planting->is_transplant ? 'checked' : '' ?>>Yes</input>
                <input type="radio" name="is_transplant" value="No" <?= !$planting->is_transplant ? 'checked' : '' ?>>No</input>
            </label>
        </p>

        <p>
            <label>
                Tags (comma separated):<br>
                <textarea cols="30" rows="2" name="tags"><?= implode(", ", $planting->tags) ?></textarea>
            </label>
        </p>

        <p>
            <label>
                Notes:<br>
                <textarea cols="50" rows="6" name="notes"><?= $planting->notes ?></textarea>
            </label>
        </p>
    </fieldset>

    <button type="submit">Update Planting</button>
</form>
