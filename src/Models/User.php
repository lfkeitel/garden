<?php

declare(strict_types=1);

namespace Garden\Models;

use MongoDB\Model\BSONDocument;

class User extends DBRecord
{
    public \DateTimeImmutable $created;
    public string $username;
    public string $fullname;
    private string $password;
    public string $role;

    protected function load_from_record(BSONDocument $record, array $extras): void
    {
        $this->id = $record['_id'];
        $this->created = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $record['created']);
        $this->username = $record['username'];
        $this->fullname = $record['fullname'];
        $this->password = $record['password'];
        $this->role = $record['role'];
    }

    public function set_password(string $password): void
    {
        $this->password = \password_hash($password, \PASSWORD_BCRYPT);
    }

    public function valid_password(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    public function to_array(): array
    {
        return [
            'created' => $this->created->format('Y-m-d H:i:s'),
            'username' => $this->username,
            'fullname' => $this->fullname,
            'password' => $this->password,
            'role' => $this->role,
        ];
    }

    public function display_string(): string
    {
        return $this->fullname == '' ? $this->username : $this->fullname;
    }
}
