<?php $this->layout('main', [
    'title' => "Plantings",
    'scripts' => ['plantings']
]) ?>

<?php if ($this->is_logged_in()) : ?>
<a href="<?= $basepath ?>/plantings/new" class="btn">New Planting</a>
<?php endif ?>

<p>
<form>
    <label>
        Status:

        <select name="filter">
            <option value='All'>All</option>
            <?php foreach ($planting_statuses as $status) : ?>
            <option <?= $filter === $status ? 'selected' : '' ?>><?= $status ?></option>
            <?php endforeach ?>
        </select>
    </label>

    <button type="submit">Filter</button>
</form>

<?php if ($this->is_logged_in()) : ?>
<form>
    <input type="hidden" value="bulk_edit" name="action">
    <button type="button" class="btn btn-small" id="bulk_edit_btn">Bulk Edit</button>
    <button type="button" class="btn btn-small" id="bulk_delete_btn">Bulk Delete</button>
    <button type="button" class="btn btn-small" id="bulk_log_btn">Bulk Logs</button>
</form>
<?php endif ?>
</p>

<div>
    <strong>Tags:</strong>
    <a href="/plantings?<?= $no_tag_link ?>">None</a><?php foreach ($allTags as $key => $tag) : ?>, <a
        href="/plantings?<?= $_SERVER['QUERY_STRING'] ?>&tag=<?= $tag ?>"><?= $tag ?></a><?php endforeach ?>
</div>

<table class="seed-table">
    <thead>
        <tr>
            <th scope="col"></th>
            <?php if ($this->is_logged_in()) : ?>
            <th scope="col">
                <form><input type="checkbox" id="select_all"></form>
            </th>
            <?php endif ?>
            <th scope="col">
                <a
                    href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=date<?= $sort_by == 'date' && $sort_dir == -1 ? '&sort_dir=1' : '' ?>">Planted</a>
            </th>
            <th scope="col">
                Sprouted
            </th>
            <th scope="col">
                <a
                    href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=status<?= $sort_by == 'status' && $sort_dir == -1 ? '&sort_dir=1' : '' ?>">Status</a>
            </th>
            <th scope="col">
                Seed
            </th>
            <th scope="col">
                Count
            </th>
            <th scope="col">
                Bed
            </th>
            <th scope="col">
                <a
                    href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=is_transplant<?= $sort_by == 'is_transplant' && $sort_dir == -1 ? '&sort_dir=1' : '' ?>">Is
                    Transplant?</a>
            </th>
            <th scope="col">
                <a
                    href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=notes<?= $sort_by == 'notes' && $sort_dir == -1 ? '&sort_dir=1' : '' ?>">Notes</a>
            </th>
            <th scope="col">
                Tags
            </th>
            <th scope="col">
                <a
                    href="<?= $basepath ?>/plantings?filter=<?= $filter ?>&sort_by=harvest_date<?= $sort_by == 'harvest_date' && $sort_dir == -1 ? '&sort_dir=1' : '' ?>">Maturity
                    Date</a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($allPlantings as $planting) : ?>
        <tr>
            <td>
                <div class="control-cell">
                    <a class="btn btn-small" href="<?= $basepath ?>/plantings/<?= $planting->get_id() ?>">Open</a>

                    <?php if ($this->is_logged_in()) : ?>
                    <form method="get" action="/plantings/edit/<?= $this->e($planting->get_id()) ?>">
                        <button type="submit" class="btn btn-small">Edit</button>
                    </form>

                    <form method="post" onsubmit="return form_confirm(this);">
                        <input type="hidden" value="delete_planting" name="action">
                        <input type="hidden" value="<?= $planting->get_id() ?>" name="planting_id">
                        <button type="submit" class="btn btn-small">Delete</button>
                    </form>
                </div>
            </td>
            <td>
                <input type="checkbox" name="plantings_selection" value="<?= $this->e($planting->get_id()) ?>">
                <?php endif ?>
            </td>
            <td><?= $planting->date->format('Y-m-d') ?></td>
            <td><?= $planting->sprout_date ? $planting->sprout_date->format('Y-m-d') : "Not yet" ?></td>
            <td><?= $planting->status ?></td>
            <td><?= $planting->seed->display_string() ?></td>
            <td><?= $planting->count ?></td>
            <td><?= $planting->bed ? $planting->bed->name : 'Deleted bed' ?></td>
            <td><?= $planting->is_transplant ? 'Yes' : 'No' ?></td>
            <td><?= $planting->notes ?></td>
            <td><?= $planting->tags_to_str() ?></td>
            <td><?= is_null($planting->harvest_date) ? '<i>' . $this->plant_maturity_day($planting) . '*</i>' : $this->plant_maturity_day($planting) ?>
            </td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>

<p>
    * Estimated maturity date.
</p>
