<?php $this->layout('main',
    ['scripts' => [],
     'title' => "Transplant {$planting->display_string()}"]) ?>

<h2>Transplant <?= $planting->display_string() ?></h2>

<form method="POST" id="planting-form">
    <fieldset>
        <p>
            <label>
                Transplant Date: <input type="date" name="transplant_date" value="<?= (new DateTimeImmutable())->format("Y-m-d") ?>">
            </label>
        </p>
    </fieldset>

    <fieldset>
        <legend>Previous Location</legend>

        <p>
            <label>
                Bed:
                <select name="old_bed">
                    <?php foreach ($beds as $bed): ?>
                    <option value="<?= $bed['id'] ?>" <?= $planting->bed->get_id() == $bed['id'] ? 'selected' : '' ?>><?= $bed['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                Row: <input type="number" name="old_row" value="<?= $planting->row ?>">
            </label>
        </p>
        <p>
            <label>
                Column: <input type="number" name="old_column" value="<?= $planting->column ?>">
            </label>
        </p>
        <p>
            <label>
                Tray ID: <input type="text" name="old_tray_id" value="<?= $planting->tray_id ?>" placeholder="Tray #-Cell block">
            </label>
        </p>
    </fieldset>

    <fieldset>
        <legend>New Location</legend>

        <p>
            <label>
                Bed:
                <select name="new_bed">
                    <?php foreach ($beds as $bed): ?>
                    <option value="<?= $bed['id'] ?>"><?= $bed['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                Row: <input type="number" name="new_row" value="1">
            </label>
        </p>
        <p>
            <label>
                Column: <input type="number" name="new_column" value="1">
            </label>
        </p>
        <p>
            <label>
                Tray ID: <input type="text" name="new_tray_id" value="" placeholder="Tray #-Cell block">
            </label>
        </p>
    </fieldset>

    <fieldset>
        <p>
            <label>
                Notes:<br>
                <textarea cols="50" rows="6" name="notes"></textarea>
            </label>
        </p>
    </fieldset>

    <button type="submit">Update Planting</button>
</form>
