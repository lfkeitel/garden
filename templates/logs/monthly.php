<?php $this->layout('main', ['title' => "Logs Monthly View"]) ?>

<h2>== Garden Logs Monthly View - <?= count($logs) ?> Logs ==</h2>

<form>
    <label>
        Month:
        <select name="month">
            <option value="01" <?= $month === '01' ? 'selected' : '' ?>>January</option>
            <option value="02" <?= $month === '02' ? 'selected' : '' ?>>February</option>
            <option value="03" <?= $month === '03' ? 'selected' : '' ?>>March</option>
            <option value="04" <?= $month === '04' ? 'selected' : '' ?>>April</option>
            <option value="05" <?= $month === '05' ? 'selected' : '' ?>>May</option>
            <option value="06" <?= $month === '06' ? 'selected' : '' ?>>June</option>
            <option value="07" <?= $month === '07' ? 'selected' : '' ?>>July</option>
            <option value="08" <?= $month === '08' ? 'selected' : '' ?>>August</option>
            <option value="09" <?= $month === '09' ? 'selected' : '' ?>>September</option>
            <option value="10" <?= $month === '10' ? 'selected' : '' ?>>October</option>
            <option value="11" <?= $month === '11' ? 'selected' : '' ?>>November</option>
            <option value="12" <?= $month === '12' ? 'selected' : '' ?>>December</option>
        </select>
    </label>

    <label>
        Year:
        <select name="year">
            <option>2024</option>
            <option>2023</option>
            <option>2022</option>
        </select>
    </label>

    <button type="submit">Search</button>
</form>

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
            <?php foreach($logs as $log): ?>
                <?php if ($log->date->format('d') == ($i - $start_day)): ?>
                    <li>
                        <span title="<?= $log->notes ?>">
                            <a href="<?= $basepath ?>/logs/<?= $log->get_id() ?>"><?= $log->display_string() ?></a>
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
