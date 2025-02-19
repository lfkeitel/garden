<?php $this->layout('main', ['title' => "Edit Bed = {$bed->display_string()}"]) ?>

<h2>Update Bed</h2>

<form method="POST" id="bed-form">
    <fieldset>
        <legend>Properties</legend>

        <p>
            <label>
                Name: <input type="text" name="name" value="<?= $bed->name ?>" required>
            </label>
        </p>
        <p>
            <label>
                Garden:
                <select name="garden">
                    <?php var_dump($gardens); ?>
                    <?php foreach ($gardens as $garden) : ?>
                        <option value="<?= $garden['id'] ?>" <?= ($bed->garden ? $bed->garden->get_id() : '') == $garden['id'] ? 'selected' : '' ?>><?= $garden['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                Rows: <input type="number" name="rows" value="<?= $bed->rows ?>">
            </label>
        </p>
        <p>
            <label>
                Columns: <input type="number" name="cols" value="<?= $bed->cols ?>">
            </label>
        </p>

        <p>
            <label>
                Hidden from Home: <input type="checkbox" name="hide_from_home" <?= $bed->hide_from_home ? 'checked' : ''  ?>>
            </label>
        </p>

        <p>
            <label>
                Notes:<br>
                <textarea cols="50" rows="6" name="notes"><?= $bed->notes ?></textarea>
            </label>
        </p>
    </fieldset>

    <button type="submit">Update Bed</button>
</form>
