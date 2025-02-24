<?php $this->layout('main', ['title' => 'Gardens']) ?>

<?php if ($this->is_logged_in()): ?>
<a href="<?= $basepath ?>/gardens/new" class="btn">New Garden</a>
<?php endif ?>

<table class="seed-table">
    <thead>
        <tr>
            <?php if ($this->is_logged_in()): ?>
            <th scope="col"></th>
            <?php endif ?>
            <th scope="col">
                <a href="<?= $basepath ?>/gardens?sort_by=name<?= $sort_by == 'name' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Name</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/gardens?sort_by=added<?= $sort_by == 'added' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Added</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/gardens?sort_by=rows<?= $sort_by == 'rows' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Rows</a>
            </th>
            <th scope="col">
                <a href="<?= $basepath ?>/gardens?sort_by=cols<?= $sort_by == 'cols' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Columns</a>
            </th>
            <th scope="col">Hidden</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($all_gardens as $garden): ?>
        <tr>
            <?php if ($this->is_logged_in()): ?>
            <td>
                <div class="control-cell">
                    <form method="get" action="/gardens/edit/<?= $this->e($garden->get_id()) ?>">
                        <button type="submit" class="btn btn-small">Edit</button>
                    </form>

                    <form method="post" onsubmit="return form_confirm(this);">
                        <input type="hidden" value="delete_garden" name="action">
                        <input type="hidden" value="<?= $garden->get_id() ?>" name="garden_id">
                        <button type="submit" class="btn btn-small">Delete</button>
                    </form>
                </div>
            </td>
            <?php endif ?>
            <td><a href="<?= $basepath ?>/gardens/<?= $garden->get_id() ?>"><?= $garden->name ?></a></td>
            <td><?= $garden->added->format('Y-m-d') ?></td>
            <td><?= $garden->rows ?></td>
            <td><?= $garden->cols ?></td>
            <td><?= $garden->hide_from_home ? 'Yes' : 'No' ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
