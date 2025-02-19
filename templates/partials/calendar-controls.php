<?php
if (!isset($month)) {
    $month = intval((new DateTimeImmutable())->format("m"));
}
?>

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
