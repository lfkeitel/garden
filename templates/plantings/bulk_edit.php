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

    <button type="submit">Update Plantings</button>
</form>
