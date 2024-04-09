<?php

declare(strict_types=1);

namespace Garden;

use Garden\Models\User;

require '../src/include.php';

$username = \readline("Username: ");

$user = $db->users->find_one("username", $username);

if (!$user) {
    echo "User {$username} not found\n";
    exit(1);
}

$db->users->delete($user);
echo "User {$username} deleted\n";
