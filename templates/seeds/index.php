<?php $this->layout('main', ['title' => "Seeds"]) ?>

<a href="/seeds/new" class="btn">New Seed</a>

<table class="seed-table">
    <thead>
        <tr>
            <th scope="col">Sort ></th>
            <th scope="col">
                <a href="/seeds?sort_by=type<?= $sort_by == 'type' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Type</a>
            </th>
            <th scope="col">
                <a href="/seeds?sort_by=common_name<?= $sort_by == 'common_name' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Common Name</a>
            </th>
            <th scope="col">
                <a href="/seeds?sort_by=variety<?= $sort_by == 'variety' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Variety</a>
            </th>
            <th scope="col">
                <a href="/seeds?sort_by=days_to_maturity<?= $sort_by == 'days_to_maturity' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Days to Maturity</a>
            </th>
            <th scope="col">
                <a href="/seeds?sort_by=days_to_germination<?= $sort_by == 'days_to_germination' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Days to Germination</a>
            </th>
            <th scope="col">
                <a href="/seeds?sort_by=is_heirloom<?= $sort_by == 'is_heirloom' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Heirloom?</a>
            </th>
            <th scope="col">
                <a href="/seeds?sort_by=sun<?= $sort_by == 'sun' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Sun</a>
            </th>
            <th scope="col">
                <a href="/seeds?sort_by=season<?= $sort_by == 'season' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Seasons</a>
            </th>
            <th scope="col">
                <a href="/seeds?sort_by=characteristics<?= $sort_by == 'characteristics' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Characteristics</a>
            </th>
            <th scope="col">
                <a href="/seeds?sort_by=is_hybrid<?= $sort_by == 'is_hybrid' && $sort_dir == 1 ? '&sort_dir=-1' : '' ?>">Hybrid?</a>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($allSeeds as $seed): ?>
        <tr>
            <td class="control-cell">
                <form method="get" action="/seeds/edit/<?= $this->e($seed->get_id()) ?>">
                    <button type="submit" class="btn btn-small">Edit</button>
                </form>

                <form method="post">
                    <input type="hidden" value="delete_seed" name="action">
                    <input type="hidden" value="<?= $seed->get_id() ?>" name="seed_id">
                    <button type="submit" class="btn btn-small">Delete</button>
                </form>
            </td>
            <td><?= $seed->type ?></td>
            <td><?= $seed->common_name ?></td>
            <td><a href="/seeds/<?= $seed->get_id() ?>"><?= $seed->variety ?></a></td>
            <td><?= $seed->days_to_maturity ?> (<i><?= $this->date_plus_days((new DateTimeImmutable()), $seed->days_to_maturity) ?></i>)</td>
            <td><?= $seed->days_to_germination ?></td>
            <td><?= $seed->is_heirloom ? 'Yes' : 'No' ?></td>
            <td><?= $seed->sun ?></td>
            <td><?= implode(", ", $seed->season) ?></td>
            <td><?= implode(", ", $seed->characteristics) ?></td>
            <td><?= $seed->is_hybrid ? 'Yes' : 'No' ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
</table>
