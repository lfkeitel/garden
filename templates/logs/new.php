<?php $this->layout(
    'main',
    [
        'styles' => ['new-log'],
        'scripts' => ['new-log'],
        'title' => 'New Log'
    ]
);
?>

<h2>New Log</h2>

<form method="POST" id="log-form">
    <fieldset>
        <legend>Properties</legend>

        <p>
            <label>
                Date: <input type="datetime-local" name="log_date"
                    value="<?= (new DateTimeImmutable())->format("Y-m-d\\TH:m") ?>">
            </label>
        </p>

        <p>
            <label>
                Planting:
                <select name="planting">
                    <option>All</option>
                    <?php foreach ($plantings as $planting) : ?>
                    <option
                        value="<?= $planting['id'] ?>"
                        <?= $select_planting === $planting['id'] ? 'selected' : '' ?>><?= $planting['name'] ?>
                    </option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>

        <p>
            <label>
                Planting Tag:
                <select name="planting_tag">
                    <option value="">None</option>
                    <?php foreach ($planting_tags as $tag) : ?>
                    <option><?= $tag ?></option>
                    <?php endforeach ?>
                </select>
            </label>
        </p>

        <p>
            <label>
                Notes:<br>
                <textarea cols="50" rows="6" name="notes"></textarea>
            </label>
        </p>

        <p>
            <span id="upload_msg"></span>
        </p>
    </fieldset>

    <fieldset>
        <legend>Upload Images</legend>

        <input type="file" name="garden_image" id="garden_image">
        <button id="upload-img-btn" type="button">Upload Image</button>
    </fieldset>

    <fieldset>
        <legend>Camera</legend>

        <p>
            <button id="start-video-btn">Start Camera</button>
            <button id="stop-video-btn">Stop Camera</button>
        </p>

        <div>
            <div class="camera">
                <video id="video" playsinline>Video stream not available.</video>
                <button id="take-photo-btn">Take photo</button>
            </div>

            <canvas id="canvas"></canvas>

            <div class="output">
                <img id="photo" alt="The screen capture will appear in this box.">
                <button id="upload-cam-btn" type="button">Upload Camera Image</button>
            </div>
        </div>

        <input type="hidden" id="image_files" name="image_files" value="">
    </fieldset>

    <input type="hidden" name="selected" value="<?= $rest_of_plantings ?>">
    <button type="submit">Add Log</button>
</form>
