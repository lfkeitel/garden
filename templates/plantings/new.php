<?php $this->layout('main',
    ['scripts' => ['seed-form'],
     'title' => "New Planting"]) ?>

<h2>New Planting</h2>

<form method="POST" id="planting-form">
    <fieldset>
        <legend>Crop</legend>

        <label for="seed">Seed:</label>

        <select name="seed">
            <?php foreach ($seeds as $seed): ?>
            <option value="<?= $seed['id'] ?>"><?= $seed['name'] ?></option>
            <?php endforeach ?>
        </select>
    </fieldset>

    <fieldset>
        <legend>Planting Location</legend>

        <p>
            <label>
                Bed:
                <select name="bed">
                    <?php foreach ($beds as $bed): ?>
                    <option value="<?= $bed['id'] ?>" <?= $bed['id'] === $auto_bed ? 'selected' : '' ?>><?= $bed['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                Row: <input type="number" name="row" value="1">
            </label>
        </p>
        <p>
            <label>
                Column: <input type="number" name="column" value="1">
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
                Planting Date: <input type="date" name="planting_date" value="<?= (new DateTimeImmutable())->format("Y-m-d") ?>">
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
                Notes:<br>
                <textarea cols="50" rows="6" name="notes"></textarea>
            </label>
        </p>
    </fieldset>

    <button type="submit">Add Planting</button>
</form>
