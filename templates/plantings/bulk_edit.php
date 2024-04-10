<?php $this->layout(
    'main',
    [
        'title' => "Bulk Edit Plantings"
    ]
) ?>

<h2>Update Plantings</h2>

<p>
    Selected plantings:

<ul>
    <?php foreach ($plantings as $planting) : ?>
        <li><?= $planting->display_string() ?></li>
    <?php endforeach ?>
</ul>
</p>

<form method="POST" id="planting-form">
    <fieldset>
        <legend>Crop</legend>

        <p>
            <label>
                Status:

                <select name="status">
                    <option selected>Change:</option>
                    <?php foreach ($planting_statuses as $status) : ?>
                        <option><?= $status ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>
    </fieldset>

    <fieldset>
        <legend>Other Information</legend>
        <p>
            <label>
                Tags (comma separated):<br>
                <textarea cols="30" rows="2" name="tags"></textarea>
            </label>
        </p>
    </fieldset>

    <button type="submit">Update Plantings</button>
</form>
