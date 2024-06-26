<?php $this->layout(
    'main',
    [
        'scripts' => ['new-planting'],
        'title' => "New Planting"
    ]
) ?>

<h2>New Planting</h2>

<form method="POST" id="planting-form">
    <fieldset>
        <legend>Crop</legend>

        <p>
            <label for="seed">
                Seed:

                <select name="seed">
                    <?php foreach ($seeds as $seed) : ?>
                    <option value="<?= $seed['id'] ?>" <?= $seed['id'] === $auto_seed ? 'selected' : '' ?>><?= $seed['name'] ?>
                    </option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>

        <p>
            <label>
                Parent Planting:

                <select name="parent">
                    <option value="">Loading...</a>
                </select>
            </label>
        </p>

        <p>
            <label>
                Plant Count: <input type="number" name="count" value="1">
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
                    <option value="<?= $bed['id'] ?>" <?= $bed['id'] === $auto_bed ? 'selected' : '' ?>>
                        <?= $bed['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                Row: <input type="text" name="row" value="<?= isset($auto_row) ? $auto_row : '1' ?>">
            </label>
        </p>
        <p>
            <label>
                Column: <input type="text" name="column" value="<?= isset($auto_col) ? $auto_col : '1' ?>">
            </label>
        </p>
        <p>
            <label>
                Tray ID: <input type="text" name="tray_id" value="" placeholder="Tray #-Cell block">
            </label>
        </p>
    </fieldset>

    <fieldset>
        <legend>Other Information</legend>

        <p>
            <label>
                Planting Date: <input type="date" name="planting_date"
                    value="<?= (new DateTimeImmutable())->format("Y-m-d") ?>">
            </label>
        </p>

        <p>
            <label>
                Is transplant?

                <input type="radio" name="is_transplant" value="Yes">Yes</input>
                <input type="radio" name="is_transplant" value="No" checked>No</input>
            </label>
        </p>

        <p>
            <label>
                Tags (comma separated):<br>
                <textarea cols="30" rows="2" name="tags"></textarea>
            </label>
        </p>

        <p>
            <label>
                Notes:<br>
                <textarea cols="50" rows="6" name="notes"></textarea>
            </label>
        </p>
    </fieldset>

    <button type="submit">Add Planting</button>
</form>
