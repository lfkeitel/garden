<?php $this->layout(
    'main',
    [
        'title' => "Bulk Edit Beds"
    ]
) ?>

<h2>Update Beds</h2>

<p>
    Selected beds:

    <ul>
        <?php foreach ($beds as $bed) : ?>
            <li><?= $bed->display_string() ?></li>
        <?php endforeach ?>
    </ul>
</p>

<form method="POST" id="planting-form">
    <fieldset>
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
    </fieldset>

    <button type="submit">Update Beds</button>
</form>
