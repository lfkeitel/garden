<?php $this->layout('main', ['title' => 'Logs']) ?>

<form>
    <label>
        Start Date: <input type="date" name="start_date" value="<?= $start_date ?>">
    </label>

    <label>
        End Date: <input type="date" name="end_date" value="<?= $end_date ?>">
    </label>

    <button type="submit">Search</button>
</form>

<a href="/logs/gallery" class="btn">Photo Gallery</a>

<?php $this->insert('partials::log-table', ['logs' => $all_logs]) ?>
