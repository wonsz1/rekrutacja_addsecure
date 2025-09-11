<?php

namespace App;

use PDO;

class SQLiteConnection
{
    private ?PDO $pdo = null;

    public function connect(): PDO
    {
        if ($this->pdo === null) {
            $this->pdo = new PDO('sqlite:db/assqlite.db');
        }

        return $this->pdo;
    }
}
