<?php $this->layout(
    'main',
    ['scripts' => ['seed-form'],
     'title' => 'New seed']
) ?>

<form method="POST" id="seed-form">
    <fieldset>
        <legend>Crop Type</legend>

        <p>
            <label>
                Wishlist Item:
                <input type="checkbox" name="on_wishlist">
            </label>
        </p>

        <label>
            Type:
            <select name="seed_type">
                <?php foreach ($seed_data['types'] as $type): ?>
                <option><?= $type ?></option>
                <?php endforeach ?>
            </select>
        </label>
    </fieldset>

    <fieldset>
        <legend>Name</legend>

        <p>
            <label>Common Name:</label>

            <select name="seed_vegetable_name">
                <?php foreach ($seed_data['common_names']['veggie'] as $seed_name): ?>
                <option><?= $seed_name ?></option>
                <?php endforeach ?>
            </select>

            <select name="seed_herb_name">
                <?php foreach ($seed_data['common_names']['herb'] as $seed_name): ?>
                <option><?= $seed_name ?></option>
                <?php endforeach ?>
            </select>

            <select name="seed_fruit_name">
                <?php foreach ($seed_data['common_names']['fruit'] as $seed_name): ?>
                <option><?= $seed_name ?></option>
                <?php endforeach ?>
            </select>

            <select name="seed_flower_name">
                <?php foreach ($seed_data['common_names']['flower'] as $seed_name): ?>
                <option><?= $seed_name ?></option>
                <?php endforeach ?>
            </select>
        </p>

        <p>
            <label for="variety_name">
                Variety: <input type="text" name="variety_name" required>
            </label>
        </p>
    </fieldset>

    <fieldset>
        <legend>Growth Characteristics</legend>

        <p>
            <label>
                Days to Germination: <input type="number" name="days_to_germination" value="0">
            </label>
        </p>
        <p>
            <label>
                Days to Maturity: <input type="number" name="days_to_maturity" value="0">
            </label>
        </p>
        <p>
            <label>
                Sun amount:

                <input type="radio" name="sun_amt" value="Full Sun" checked>Full Sun</input>
                <input type="radio" name="sun_amt" value="Shaded">Shaded</input>
            </label>
        </p>
        <p>
            <label>
                Preferred Growing Seasons:

                <input type="checkbox" name="growing_season[]" value="Spring">Spring</input>
                <input type="checkbox" name="growing_season[]" value="Summer" checked>Summer</input>
                <input type="checkbox" name="growing_season[]" value="Fall">Fall</input>
                <input type="checkbox" name="growing_season[]" value="Winter">Winter</input>
            </label>
        </p>
    </fieldset>

    <fieldset>
        <legend>Seed Properties</legend>

        <p>
            <label>
                Source: <input type="text" name="source">
            </label>
        </p>

        <p>
            <label>
                Source URL: <input type="text" name="source_link">
            </label>
        </p>

        <p>
            <label>
                Is the seed heirloom?

                <input type="radio" name="is_heirloom" value="Yes" checked>Yes</input>
                <input type="radio" name="is_heirloom" value="No">No</input>
            </label>
        </p>
        <p>
            <label>
                Is the seed a hybrid?

                <input type="radio" name="is_hybrid" value="Yes">Yes</input>
                <input type="radio" name="is_hybrid" value="No" checked>No</input>
            </label>
        </p>
        <p>
            <label>
                Other Characteristics:

                <input type="checkbox" name="other_charact[]" value="Heat tolerant">Heat tolerant</input>
                <input type="checkbox" name="other_charact[]" value="Frost tolerant">Frost tolerant</input>
                <input type="checkbox" name="other_charact[]" value="Bolt resistant">Bolt resistant</input>
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

    <button type="submit">Add Seed</button>
</form>
