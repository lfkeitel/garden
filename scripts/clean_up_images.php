<?php
declare(strict_types=1);
namespace Garden;

$dev_mode = false;

$cwd = dirname(__FILE__);
$upload_dir = "{$cwd}/../uploads";
require "{$cwd}/../vendor/autoload.php";
require "{$cwd}/../src/config.php";

if ($dev_mode) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

require "{$cwd}/../src/functions.php";
require "{$cwd}/../src/database.php";

$db = new DatabaseConnection($mongo_db_connect);

$logs = $db->logs->get_all();
$log_images = [];

foreach ($logs as $log) {
    foreach ($log->image_files as $image) {
        $log_images []= $image;
    }
}

$files = \scandir($upload_dir);

$log_image_cnt = count($log_images);
$file_cnt = count($files)-2;

echo "{$log_image_cnt} linked images\n";
echo "{$file_cnt} uploaded images\n";

foreach($files as $file) {
    if (!\str_contains($file, ".png")) {
        continue;
    }

    if (!\in_array($file, $log_images)) {
        echo "Delete {$file}\n";
        \unlink("{$upload_dir}/$file");
    }
}
