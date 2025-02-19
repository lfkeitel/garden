<?php $this->layout('main', ['title' => "Edit Garden = {$garden->display_string()}"]) ?>

<h2>Update Garden</h2>

<form method="POST" id="garden-form">
    <fieldset>
        <legend>Properties</legend>

        <p>
            <label>
                Name: <input type="text" name="name" value="<?= $garden->name ?>" required>
            </label>
        </p>
        <p>
            <label>
                Rows: <input type="number" name="rows" value="<?= $garden->rows ?>">
            </label>
        </p>
        <p>
            <label>
                Columns: <input type="number" name="cols" value="<?= $garden->cols ?>">
            </label>
        </p>

        <p>
            <label>
                Hidden from Home: <input type="checkbox" name="hide_from_home" <?= $garden->hide_from_home ? 'checked' : ''  ?>>
            </label>
        </p>

        <p>
            <label>
                Notes:<br>
                <textarea cols="50" rows="6" name="notes"><?= $garden->notes ?></textarea>
            </label>
        </p>
    </fieldset>

    <button type="submit">Update Garden</button>
</form>
