<?php $this->layout('main', ['title' => 'Beds']) ?>

<a href="/beds/new" class="btn">New Bed</a>

<table class="seed-table">
    <thead>
        <tr>
            <th scope="col"></th>
            <th scope="col">
                <a href="/beds?sort_by=name<?= $sort_by == 'name' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Name</a>
            </th>
            <th scope="col">
                <a href="/beds?sort_by=added<?= $sort_by == 'added' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Added</a>
            </th>
            <th scope="col">
                <a href="/beds?sort_by=rows<?= $sort_by == 'rows' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Rows</a>
            </th>
            <th scope="col">
                <a href="/beds?sort_by=cols<?= $sort_by == 'cols' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Columns</a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($all_beds as $bed): ?>
        <tr>
            <td class="control-cell">
                <form method="get" action="/beds/edit/<?= $this->e($bed->get_id()) ?>">
                    <button type="submit" class="btn btn-small">Edit</button>
                </form>

                <form method="post" onsubmit="return form_confirm(this);">
                    <input type="hidden" value="delete_bed" name="action">
                    <input type="hidden" value="<?= $bed->get_id() ?>" name="bed_id">
                    <button type="submit" class="btn btn-small">Delete</button>
                </form>
            </td>
            <td><a href="/beds/<?= $bed->get_id() ?>"><?= $bed->name ?></a></td>
            <td><?= $bed->added->format('Y-m-d') ?></td>
            <td><?= $bed->rows ?></td>
            <td><?= $bed->cols ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
