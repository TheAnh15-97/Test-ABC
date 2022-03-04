<?php

namespace Core;

use App\Config;
use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private ?PDO $dbHandler = null;
    private PDOStatement $stmt;

    public function __construct()
    {
        try {
            if (!isset($this->dbHandler)) {
                $this->dbHandler = new PDO("sqlite:" . Config::PATH_TO_SQLITE_FILE);
            }

        } catch (PDOException $e) {
            throw new $e;
        }
    }

    public function query($sql)
    {
        $this->stmt = $this->dbHandler->prepare($sql);
    }

    public function bind($param, $value, $type = null)
    {
        if (is_null($type)) {
            $type = match (true) {
                is_int($value) => PDO::PARAM_INT,
                is_bool($value) => PDO::PARAM_BOOL,
                is_null($value) => PDO::PARAM_NULL,
                default => PDO::PARAM_STR,
            };
        }

        $this->stmt->bindValue($param, $value, $type);
    }

    public function execute(): bool
    {
        return $this->stmt->execute();
    }

    public function resultSet(): bool|array
    {
        $this->execute();

        return $this->stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function single(): mixed
    {
        $this->execute();

        return $this->stmt->fetch(PDO::FETCH_OBJ);
    }

    public function rowCount(): int
    {
        return $this->stmt->rowCount();
    }
}