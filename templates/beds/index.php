<?php $this->layout('main', [
    'title' => 'Beds',
    'scripts' => ['beds']
]) ?>

<?php if ($this->is_logged_in()): ?>
<a href="<?= $basepath ?>/beds/new" class="btn">New Bed</a>

<p>
    <form>
        <input type="hidden" value="bulk_edit" name="action">
        <button type="button" class="btn btn-small" id="bulk_edit_btn">Bulk Edit</button>
        <button type="button" class="btn btn-small" id="bulk_delete_btn">Bulk Delete</button>
    </form>
</p>
<?php endif ?>

<table class="seed-table">
    <thead>
        <tr>
            <?php if ($this->is_logged_in()): ?>
            <th scope="col"></th>
            <th scope="col">
                <form><input type="checkbox" id="select_all"></form>
            </th>
            <?php endif ?>
            <th scope="col">
                <a href="<?= $basepath ?>/beds?sort_by=name<?= $sort_by == 'name' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Name</a>
            </th>
            <th scope="col">
                Garden
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/beds?sort_by=rows<?= $sort_by == 'rows' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Rows</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/beds?sort_by=added<?= $sort_by == 'added' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Added</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/beds?sort_by=cols<?= $sort_by == 'cols' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Columns</a>
            </th>
            <th scope="col">Hidden</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($all_beds as $bed): ?>
        <tr>
            <?php if ($this->is_logged_in()): ?>
            <td>
                <div class="control-cell">
                    <form method="get" action="/beds/edit/<?= $this->e($bed->get_id()) ?>">
                        <button type="submit" class="btn btn-small">Edit</button>
                    </form>

                    <form method="post" onsubmit="return form_confirm(this);">
                        <input type="hidden" value="delete_bed" name="action">
                        <input type="hidden" value="<?= $bed->get_id() ?>" name="bed_id">
                        <button type="submit" class="btn btn-small">Delete</button>
                    </form>
                </div>
            </td>
            <td>
                <input type="checkbox" name="beds_selection" value="<?= $this->e($bed->get_id()) ?>">
            </td>
            <?php endif ?>
            <td><a href="<?= $basepath ?>/beds/<?= $bed->get_id() ?>"><?= $bed->name ?></a></td>
            <td><a href="/gardens/<?= $bed->garden->get_id() ?>"><?= $bed->garden->display_string() ?></a></td>
            <td><?= $bed->rows ?></td>
            <td><?= $bed->cols ?></td>
            <td><?= $bed->added->format('Y-m-d') ?></td>
            <td><?= $bed->hide_from_home ? 'Yes' : 'No' ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
