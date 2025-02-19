<table class="calendar">
    <tr>
        <th>Sunday</th>
        <th>Monday</th>
        <th>Tuesday</th>
        <th>Wednesday</th>
        <th>Thursday</th>
        <th>Friday</th>
        <th>Saturday</th>
    </tr>

    <tr>
    <?php for($i = 0; $i < $start_day; $i++): ?>
        <td>&nbsp;</td>
    <?php endfor ?>

    <?php for($i = $start_day+1; $i <= $this->days_in_month($month) + $start_day; $i++): ?>
        <td>
            <?= $i - $start_day ?>
            <ul>
            <?php foreach($items as $item): ?>
                <?php if ($item['date']->format('d') == ($i - $start_day)): ?>
                    <li>
                        <span title="<?= $item['span_title'] ?>">
                            <a href="<?= $item['link'] ?>"><?= $item['title'] ?></a>
                        </span>
                    </li>
                <?php endif ?>
            <?php endforeach ?>
            </ul>
        </td>

        <?php if ($i % 7 === 0): ?>
        </tr><tr>
        <?php endif ?>
    <?php endfor ?>

    <?php for($i = ($this->days_in_month($month) + $start_day) % 7; $i < 7; $i++): ?>
        <td>&nbsp;</td>
    <?php endfor ?>
    </tr>
<table>
