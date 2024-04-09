<?php

declare(strict_types=1);

namespace Garden;

use Garden\Models\User;

require '../src/include.php';

function read_password($prompt = null, $hide = false)
{
    echo $prompt ? $prompt : '';
    system('stty -echo');
    $input = trim(fgets(STDIN));
    system('stty echo');
    // add a new line since the users CR didn't echo
    echo "\n";
    return $input;
}

$username = \readline("Username: ");
$fullname = \readline("Full Name: ");
$password = read_password("Password: ", true);
$role = \readline("Role: ");

$user = new User();
$user->created = new \DateTimeImmutable();
$user->username = $username;
$user->fullname = $fullname;
$user->role = $role;
$user->set_password($password);
$db->users->create($user);
