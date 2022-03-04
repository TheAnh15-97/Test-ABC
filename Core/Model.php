<?php

namespace Core;

use PDOException;

abstract class Model
{
    protected Database $db;

    protected string $table = '';

    protected array $fillable = [];

    public function __construct()
    {
        $this->db = new Database;
    }

    public function all(): bool|array
    {
        try {
            $this->db->query("SELECT * FROM $this->table");

            return $this->db->resultSet();
        } catch (PDOException $e) {
            exit($e->getMessage());
        }
    }

    public function get(int $id)
    {
        $this->db->query("SELECT * FROM $this->table WHERE id = :id");
        $this->db->bind(':id', $id);

        return $this->db->single();
    }

    public function create(array $params): bool
    {
        $data = [];

        foreach ($this->fillable as $item) {
            $data[':' . $item] = $params[$item] ?? null;
        }

        $this->db->query("INSERT INTO $this->table (" . implode(',', $this->fillable) . ") VALUES (" . implode(',', array_keys($data)) . ")");

        foreach ($data as $key => $value) {
            $this->db->bind($key, $value);
        }

        return $this->db->execute();
    }

    public function delete(int $id): bool
    {
        $this->db->query("DELETE FROM $this->table WHERE id = :id");
        $this->db->bind(':id', $id);
        return $this->db->execute();
    }

    public function update(int $id, array $params): bool
    {
        $params = array_filter($params, function ($key) {
            return in_array($key, $this->fillable);
        }, ARRAY_FILTER_USE_KEY);

        $fields = [];
        foreach ($params as $key => $value) {
            $fields[] = "$key = :$key";
        }
//        die("UPDATE $this->table SET " . implode(',', $fields) . " WHERE id = :id");
        $this->db->query("UPDATE $this->table SET " . implode(',', $fields) . " WHERE id = :id");
        $this->db->bind(':id', $id);
        foreach ($params as $key => $value) {
            $this->db->bind(":$key", $value);
        }

        return $this->db->execute();
    }
}
