<?php $this->layout('main', ['title' => 'New Bed']) ?>

<h2>New Bed</h2>

<form method="POST" id="bed-form">
    <fieldset>
        <legend>Properties</legend>

        <p>
            <label>
                Name: <input type="text" name="name" value="" required>
            </label>
        </p>
        <p>
            <label>
                Garden:
                <select name="garden">
                    <?php foreach ($gardens as $garden) : ?>
                    <option value="<?= $garden['id'] ?>"><?= $garden['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>
        <p>
            <label>
                Rows: <input type="number" name="rows" value="1">
            </label>
        </p>
        <p>
            <label>
                Columns: <input type="number" name="cols" value="1">
            </label>
        </p>

        <p>
            <label>
                Notes:<br>
                <textarea cols="50" rows="6" name="notes"></textarea>
            </label>
        </p>
    </fieldset>

    <button type="submit">Add Bed</button>
</form>
