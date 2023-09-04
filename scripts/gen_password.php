<?php
declare(strict_types=1);
namespace Garden;

echo \password_hash(\readline('Password: '), \PASSWORD_BCRYPT);
echo "\n";
