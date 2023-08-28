<?php $this->layout('main',
    ['scripts' => ['seed-form'],
     'title' => "Edit Seed = {$seed->display_string()}"]) ?>

<h2>== <?= $seed->common_name ?> - <?= $seed->variety ?> <?= $seed->on_wishlist ? '(Wishlist)' : '' ?> ==</h2>

<form method="POST" id="seed-form">
    <fieldset>
        <p>
            <label>
                Wishlist Item:

                <input type="checkbox" name="on_wishlist" <?= $seed->on_wishlist ? 'checked' : '' ?>>
            </label>
        </p>

        <legend>Crop Type</legend>

        <label for="seed_type">Type:</label>

        <select name="seed_type">
            <?php foreach ($seed_data['types'] as $type): ?>
            <option <?= $seed->type == $type ? 'selected' : '' ?>><?= $type ?></option>
            <?php endforeach ?>
        </select>
    </fieldset>

    <fieldset>
        <legend>Name</legend>

        <p>
            <label>Common Name:</label>

            <select name="seed_vegetable_name">
                <?php foreach ($seed_data['common_names']['veggie'] as $seed_name): ?>
                <option <?= $seed->common_name == $seed_name ? 'selected' : '' ?>><?= $seed_name ?></option>
                <?php endforeach ?>
            </select>

            <select name="seed_herb_name">
                <?php foreach ($seed_data['common_names']['herb'] as $seed_name): ?>
                <option <?= $seed->common_name == $seed_name ? 'selected' : '' ?>><?= $seed_name ?></option>
                <?php endforeach ?>
            </select>

            <select name="seed_fruit_name">
                <?php foreach ($seed_data['common_names']['fruit'] as $seed_name): ?>
                <option <?= $seed->common_name == $seed_name ? 'selected' : '' ?>><?= $seed_name ?></option>
                <?php endforeach ?>
            </select>

            <select name="seed_flower_name">
                <?php foreach ($seed_data['common_names']['flower'] as $seed_name): ?>
                <option <?= $seed->common_name == $seed_name ? 'selected' : '' ?>><?= $seed_name ?></option>
                <?php endforeach ?>
            </select>
        </p>

        <p>
            <label for="variety_name">
                Variety: <input type="text" name="variety_name" value="<?= $seed->variety ?>" required>
            </label>
        </p>
    </fieldset>

    <fieldset>
        <legend>Growth Characteristics</legend>

        <p>
            <label>
                Days to Germination: <input type="number" name="days_to_germination" value="<?= $seed->days_to_germination ?>">
            </label>
        </p>
        <p>
            <label>
                Days to Maturity: <input type="number" name="days_to_maturity" value="<?= $seed->days_to_maturity ?>">
            </label>
        </p>
        <p>
            <label>
                Sun amount:

                <input type="radio" name="sun_amt" value="Full Sun" <?= $seed->sun == 'Full Sun' ? 'checked' : '' ?>>Full Sun</input>
                <input type="radio" name="sun_amt" value="Shaded" <?= $seed->sun == 'Shaded' ? 'checked' : '' ?>>Shaded</input>
            </label>
        </p>
        <p>
            <label>
                Preferred Growing Seasons:

                <input type="checkbox" name="growing_season[]" value="Spring" <?= in_array('Spring', $seed->season) ? 'checked' : '' ?>>Spring</input>
                <input type="checkbox" name="growing_season[]" value="Summer" <?= in_array('Summer', $seed->season) ? 'checked' : '' ?>>Summer</input>
                <input type="checkbox" name="growing_season[]" value="Fall" <?= in_array('Fall', $seed->season) ? 'checked' : '' ?>>Fall</input>
                <input type="checkbox" name="growing_season[]" value="Winter" <?= in_array('Winter', $seed->season) ? 'checked' : '' ?>>Winter</input>
            </label>
        </p>
    </fieldset>

    <fieldset>
        <legend>Seed Properties</legend>

        <p>
            <label>
                Source: <input type="text" name="source" value="<?= $seed->source ?>">
            </label>
        </p>

        <p>
            <label>
                Source URL: <input type="text" name="source_link" value="<?= $seed->link ?>">
            </label>
        </p>

        <p>
            <label>
                Is the seed heirloom?

                <input type="radio" name="is_heirloom" value="Yes" <?= $seed->is_heirloom ? 'checked' : '' ?>>Yes</input>
                <input type="radio" name="is_heirloom" value="No" <?= !$seed->is_heirloom ? 'checked' : '' ?>>No</input>
            </label>
        </p>
        <p>
            <label>
                Is the seed a hybrid?

                <input type="radio" name="is_hybrid" value="Yes" <?= $seed->is_hybrid ? 'checked' : '' ?>>Yes</input>
                <input type="radio" name="is_hybrid" value="No" <?= !$seed->is_hybrid ? 'checked' : '' ?>>No</input>
            </label>
        </p>
        <p>
            <label>
                Other Characteristics:

                <input type="checkbox" name="other_charact[]" value="Heat Tolerant" <?= in_array('Heat Tolerant', $seed->characteristics) ? 'checked' : '' ?>>Heat tolerant</input>
                <input type="checkbox" name="other_charact[]" value="Frost Tolerant" <?= in_array('Frost Tolerant', $seed->characteristics) ? 'checked' : '' ?>>Frost tolerant</input>
                <input type="checkbox" name="other_charact[]" value="Bolt Resistant" <?= in_array('Bolt Resistant', $seed->characteristics) ? 'checked' : '' ?>>Bolt resistant</input>
            </label>
        </p>

        <p>
            <label>
                Notes:<br>
                <textarea cols="50" rows="6" name="notes"><?= $seed->notes ?></textarea>
            </label>
        </p>
    </fieldset>

    <button type="submit">Save Seed</button>
</form>
