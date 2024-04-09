<?php

declare(strict_types=1);

namespace Garden;

use Garden\Models\User;

require '../src/include.php';

$users = $db->users->get_all();

echo "Username | Full name | Role\n";

foreach ($users as $user) {
    echo "{$user->username}\t| {$user->display_string()}\t| {$user->role}";
}

echo "\n";
