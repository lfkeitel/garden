<?php $this->layout('main', [
    'title' => "Plantings",
    'scripts' => ['plantings']]) ?>

<?php if ($this->is_logged_in()): ?>
<a href="<?= $basepath ?>/plantings/new" class="btn">New Planting</a>
<?php endif ?>

<p>
    <form>
        <label>
            Status:

            <select name="filter">
                <option value=''>All</option>
                <?php foreach ($planting_statuses as $status): ?>
                <option <?= $filter === $status ? 'selected' : '' ?>><?= $status ?></option>
                <?php endforeach ?>
            </select>
        </label>

        <button type="submit">Filter</button>
    </form>

    <form>
        <input type="hidden" value="bulk_edit" name="action">
        <button type="button" class="btn btn-small" id="bulk_edit_btn">Bulk Edit</button>
    </form>
</p>

<table class="seed-table">
    <thead>
        <tr>
            <?php if ($this->is_logged_in()): ?>
            <th scope="col"></th>
            <th scope="col"></th>
            <?php endif ?>
            <th scope="col">Sort ></th>
            <th scope="col">
                <a href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=date<?= $sort_by == 'date' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Planted</a>
            </th>
            <th scope="col">
                Seed
            </th>
            <th scope="col">
                Bed
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=row<?= $sort_by == 'row' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Row</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=column<?= $sort_by == 'column' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Column</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=status<?= $sort_by == 'status' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Status</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=is_transplant<?= $sort_by == 'is_transplant' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Is Transplant?</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=notes<?= $sort_by == 'notes' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Notes</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=tray_id<?= $sort_by == 'tray_id' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Tray ID</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=harvest_date<?= $sort_by == 'harvest_date' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Maturity Date</a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($allPlantings as $planting): ?>
        <tr>
            <?php if ($this->is_logged_in()): ?>
            <td class="control-cell">
                <form method="get" action="/plantings/edit/<?= $this->e($planting->get_id()) ?>">
                    <button type="submit" class="btn btn-small">Edit</button>
                </form>

                <form method="post" onsubmit="return form_confirm(this);">
                    <input type="hidden" value="delete_planting" name="action">
                    <input type="hidden" value="<?= $planting->get_id() ?>" name="planting_id">
                    <button type="submit" class="btn btn-small">Delete</button>
                </form>
            </td>
            <td>
                <input type="checkbox" name="plantings_selection" value="<?= $this->e($planting->get_id()) ?>">
            </td>
            <?php endif ?>
            <td><a href="<?= $basepath ?>/plantings/<?= $planting->get_id() ?>">View</a></td>
            <td><?= $planting->date->format('Y-m-d') ?></td>
            <td><?= $planting->seed->display_string() ?></td>
            <td><?= $planting->bed->name ?></td>
            <td><?= $planting->row ?></td>
            <td><?= $planting->column ?></td>
            <td><?= $planting->status ?></td>
            <td><?= $planting->is_transplant ? 'Yes': 'No' ?></td>
            <td><?= $planting->notes ?></td>
            <td><?= $planting->tray_id ?></td>
            <td><?= is_null($planting->harvest_date) ? '<i>'.$this->date_plus_days($planting->date, $planting->seed->days_to_maturity).'*</i>' : $planting->harvest_date->format('Y-m-d') ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<p>
    * Estimated maturity date.
</p>
